<?php
// src/Stsbl/RepositoryMonitorBundle/EventListener/IDeskListener.php
namespace Stsbl\RepositoryMonitorBundle\EventListener;

use IServ\CoreBundle\Event\IDeskEvent;
use IServ\CoreBundle\EventListener\IDeskListenerInterface;
use IServ\CoreBundle\Util\System;
use Stsbl\RepositoryMonitorBundle\Security\Privilege;

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
 * @author Felix Jacobi <felix.jacobi@stsbl.de>
 * @license MIT license <https://opensource.org/licenses/MIT>
 */
class IDeskListener implements IDeskListenerInterface
{
    use ShellTrait;
    
    /**
     * {@inheritdoc}
     */
    public function onBuildIDesk(IDeskEvent $event): void
    {
        if (!$event->getAuthorizationChecker()->isGranted(Privilege::SRV_WARN)) {
            // exit if user is not allowed to see status warnings
            return;
        }

        $mode = $this->getUpdateMode();
        
        if (System::UPDATEMODE_TESTING === $mode) {
            $event->addSidebarContent(
                'admin.stsblupdatemode',
                'StsblRepositoryMonitorBundle:Dashboard:status.html.twig',
                $this->getDashboardData($mode),
                -1000
            );
        }
    }
}
