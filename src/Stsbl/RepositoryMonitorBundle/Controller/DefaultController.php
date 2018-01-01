<?php
// src/Stsbl/RepositoryMonitorBundle/Controller/DefaultController.php
namespace Stsbl\RepositoryMonitorBundle\Controller;

use IServ\CoreBundle\Controller\PageController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;

/*
 * The MIT License
 *
 * Copyright 2018 Felix Jacobi.
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
class DefaultController extends PageController
{
    /**
     * Creates form for entering credentials
     * 
     * @return \Symfony\Component\Form\Form
     */
    private function getCredentialsForm()
    {
        /* @var $builder \Symfony\Component\Form\FormBuilder */
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
     * @param Request $request
     * @return array
     * @Route("/admin/stsblrepomon/credentials", name="admin_stsbl_repomon_credentials")
     * @Template()
     */
    public function credentialsAction(Request $request)
    {
        $form = $this->getCredentialsForm();
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            /* @var $shell \IServ\CoreBundle\Service\Shell */
            $shell = $this->get('iserv.shell');
            
            $ip = $request->getClientIp();
            $fwdIp = preg_replace("/.*,\s*/", "", @$_SERVER["HTTP_X_FORWARDED_FOR"]);
            $sessionPassword = $this->get('iserv.security_handler')->getSessionPassword();
            
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
            
            $shell->exec('closefd setsid sudo /usr/lib/iserv/stsbl_repo_store_credentials', $args, null, $env);
            
            if (count($shell->getOutput()) > 0) {
                $this->get('iserv.flash')->success(implode("\n", $shell->getOutput()));
            }
            
            if (count($shell->getError()) > 0) {
                $this->get('iserv.flash')->error(implode("\n", $shell->getError()));
            }
        } else {
            $errors = $form->getErrors(true);
            
            if (count($errors) > 0) {
                foreach ($errors as $e) {
                    $this->get('iserv.flash')->error($e->getMessage());
                }
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
