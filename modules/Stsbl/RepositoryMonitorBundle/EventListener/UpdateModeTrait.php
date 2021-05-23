<?php

declare(strict_types=1);

namespace Stsbl\RepositoryMonitorBundle\EventListener;

use IServ\CoreBundle\Exception\ShellExecException;
use IServ\CoreBundle\Service\Shell;
use IServ\CoreBundle\Util\System;

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
trait UpdateModeTrait
{
    /**
     * Get current update mode
     */
    protected function getUpdateMode(): string
    {
        return trim(file_get_contents('/etc/iserv/update'));
    }

    /**
     * Get data for dashboard
     *
     * @return string[]
     */
    protected function getDashboardData(string $mode): array
    {
        return [
            'title' => __('%s updates (StsBl repository)', $mode),
            'icon' => ['style' => 'fugue', 'name' => 'drive-globe'],
            'text' => __('Your server is currently receiving %s updates from the repository of the Stadtteilschule Blankenese.', $mode),
            'mode' => $mode,
            'link' => 'https://it.stsbl.de/documentation/general/update-mode',
            'link_text' => _('For more information please refer to our documentation'),
            'panel_class' => 'panel-warning',
        ];
    }
}
