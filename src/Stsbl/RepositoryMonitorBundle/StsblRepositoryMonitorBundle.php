<?php
// src/Stsbl/RepositoryMonitorBundle/StsblRepoMonBundle.php
namespace Stsbl\RepositoryMonitorBundle;

use Stsbl\RepositoryMonitorBundle\DependencyInjection\StsblRepositoryMonitorExtension;
use IServ\CoreBundle\Routing\AutoloadRoutingBundleInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Felix Jacobi <felix.jacobi@stsbl.de>
 * @license GNU General Public License <http://gnu.org/licenses/gpl-3.0>
 */
class StsblRepositoryMonitorBundle extends Bundle implements AutoloadRoutingBundleInterface
{
    public function getContainerExtension()
    {
        return new StsblRepositoryMonitorExtension();
    }
}
