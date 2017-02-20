<?php
// src/Stsbl/RepositoryMonitorBundle/EventListener/AdminDashboardListener.php
namespace Stsbl\RepositoryMonitorBundle\EventListener;

use IServ\AdminBundle\Event\AdminDashboardEvent;
use IServ\AdminBundle\Event\AdminHomeEvent;
use IServ\AdminBundle\EventListener\AdminDashboardListenerInterface;
use IServ\CoreBundle\Service\Shell;
use Stsbl\RepositoryMonitorBundle\Security\Privilege;

/**
 * @author Felix Jacobi <felix.jacobi@stsbl.de>
 * @license MIT license <https://opensource.org/licenses/MIT>
 */
class AdminDashboardListener implements AdminDashboardListenerInterface 
{
    use ShellTrait;
    
    /**
     * {@inheritdoc}
     */
    public function onBuildDashboard(AdminDashboardEvent $event)
    {
        if (!$event->getAuthorizationChecker()->isGranted(Privilege::SRV_WARN)) {
            // exit if user is not allowed to see status warnings
            return;
        }
        
        $mode = $this->getUpdateMode();
        
        if ($event instanceof AdminHomeEvent && 'testing' === $mode) {
            // don't add message on testing updates, as it is shown as "dangerous", IDeskListener handles this case.
            return;
        } else if ('unstable' === $mode || $mode === 'testing') {
            // Inject into admin dashboard
            $event->addContent(
                'admin.stsblupdatemode',
                'StsblRepositoryMonitorBundle:Dashboard:status.html.twig',
                $this->getDashboardData($mode)
            );
        }
    }

}
