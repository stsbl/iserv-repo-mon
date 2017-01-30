<?php
// src/Stsbl/RepositoryMonitorBundle/StsblRepoMonBundle.php
namespace Stsbl\RepositoryMonitorBundle;

use Stsbl\RepositoryMonitorBundle\DependencyInjection\StsblRepositoryMonitorExtension;
use IServ\CoreBundle\Routing\AutoloadRoutingBundleInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Felix Jacobi <felix.jacobi@stsbl.de>
 * @license MIT license <https://opensource.org/licenses/MIT>
 */
class StsblRepositoryMonitorBundle extends Bundle implements AutoloadRoutingBundleInterface
{
    public function getContainerExtension()
    {
        return new StsblRepositoryMonitorExtension();
    }
}
