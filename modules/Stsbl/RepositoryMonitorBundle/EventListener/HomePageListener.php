<?php

declare(strict_types=1);

namespace Stsbl\RepositoryMonitorBundle\EventListener;

use IServ\CoreBundle\Event\HomePageEvent;
use IServ\CoreBundle\EventListener\HomePageListenerInterface;
use IServ\CoreBundle\Util\System;
use Stsbl\RepositoryMonitorBundle\Security\Privilege;

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
 * @author Felix Jacobi <felix.jacobi@stsbl.de>
 * @license MIT license <https://opensource.org/licenses/MIT>
 */
final class HomePageListener implements HomePageListenerInterface
{
    use UpdateModeTrait;

    /**
     * {@inheritdoc}
     */
    public function onBuildHomePage(HomePageEvent $event): void
    {
        if (!$event->getAuthorizationChecker()->isGranted(Privilege::SRV_WARN)) {
            // exit if user is not allowed to see status warnings
            return;
        }

        $mode = $this->getUpdateMode();

        if (System::UPDATEMODE_TESTING === $mode) {
            $event->addSidebarContent(
                'admin.stsblupdatemode',
                '@StsblRepositoryMonitor/Dashboard/status.html.twig',
                $this->getDashboardData($mode),
                -1000
            );
        }
    }
}
