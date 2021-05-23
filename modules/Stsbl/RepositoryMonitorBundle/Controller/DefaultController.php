<?php

declare(strict_types=1);

namespace Stsbl\RepositoryMonitorBundle\Controller;

use IServ\CoreBundle\Controller\AbstractPageController;
use IServ\CoreBundle\Exception\ShellExecException;
use IServ\CoreBundle\Security\Core\SecurityHandler;
use IServ\CoreBundle\Service\Flash;
use IServ\CoreBundle\Service\Shell;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;

/*
 * The MIT License
 *
 * Copyright 2021 Felix Jacobi.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * Frontend for entering credentials
 *
 * @author Felix Jacobi <felix.jacobi@stsbl.de>
 * @license MIT license <https://opensource.org/licenses/MIT>
 */
final class DefaultController extends AbstractPageController
{
    private function getCredentialsForm(): FormInterface
    {
        $builder = $this->get('form.factory')->createNamedBuilder('stsbl_repo_mon_credentials');

        $builder
            ->add('access_number', TextType::class, [
                'label' => _('Access number'),
                'attr' => [
                    'help_text' => _('Please enter the access number which you received via e-mail.'),
                    'autocomplete' => 'false',
                ],
                'constraints' => [new NotBlank(['message' => _('Access number is required.')])]
            ])
            ->add('access_password', PasswordType::class, [
                'label' => _('Access password'),
                'attr' => [
                    'help_text' => _('Please enter the matching password for the access number.'),
                    'autocomplete' => 'new-password',
                ],
                'constraints' => [new NotBlank(['message' => _('Access password is required.')])]
            ])
            ->add('submit', SubmitType::class, [
                'label' => _('Save credentials'),
                'buttonClass' => 'btn-success',
                'icon' => 'ok',
            ])
        ;

        return $builder->getForm();
    }

    /**
     * Displays form for entering credentials.
     *
     * @Route("/admin/stsblrepomon/credentials", name="admin_stsbl_repomon_credentials")
     * @Template()
     */
    public function credentialsAction(
        Request $request,
        Flash $flash,
        SecurityHandler $securityHandler,
        Shell $shell
    ): array {
        $form = $this->getCredentialsForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $ip = $request->getClientIp();
            $fwdIp = preg_replace(
                '/.*,\s*/',
                '',
                $request->server->get('HTTP_X_FORWARDED_FOR', '')
            );
            $sessionPassword = $securityHandler->getSessionPassword();

            $args = [
                $this->getUser()->getUsername(),
                $data['access_number'],
            ];

            $env = [
                'SESSPW' => $sessionPassword,
                'ARG' => $data['access_password'],
                'IP' => $ip,
                'IPFWD' => $fwdIp,
            ];

            try {
                $shell->exec('sudo /usr/lib/iserv/stsbl_repo_store_credentials', $args, null, $env);
            } catch (ShellExecException $e) {
                throw new \RuntimeException('Failed to run stsbl_repo_store_credentials!', 0, $e);
            }

            if (!empty($shell->getOutput())) {
                $flash->success(implode("\n", $shell->getOutput()));
            }

            if (!empty($shell->getError())) {
                $flash->error(implode("\n", $shell->getError()));
            }
        }

        // track path
        $this->addBreadcrumb(_('StsBl-Repository: credentials'), $this->generateUrl('admin_stsbl_repomon_credentials'));

        return [
            'form' => $form->createView(),
            'help' => 'https://it.stsbl.de/repository/access',
        ];
    }
}
