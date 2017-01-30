<?php
// src/Stsbl/RepositoryMonitorBundle/EventListener/AdminDashboardListener.php
namespace Stsbl\RepositoryMonitorBundle\EventListener;

use IServ\AdminBundle\Event\AdminDashboardEvent;
use IServ\AdminBundle\EventListener\AdminDashboardListenerInterface;
use IServ\CoreBundle\Service\Shell;
use Stsbl\RepositoryMonitorBundle\Security\Privilege;

/**
 * @author Felix Jacobi <felix.jacobi@stsbl.de>
 * @license MIT license <https://opensource.org/licenses/MIT>
 */
class AdminDashboardListener implements AdminDashboardListenerInterface 
{
    /**
     * @var Shell
     */
    private $shell;
    
    /**
     * Inject shell into listener
     * 
     * @param Shell $shell
     */
    public function __construct(Shell $shell)
    {
        $this->shell = $shell;
    }

    /**
     * {@inheritdoc}
     */
    public function onBuildDashboard(AdminDashboardEvent $event)
    {
        if(!$event->getAuthorizationChecker()->isGranted(Privilege::SRV_WARN)) {
            // exit if user is not allowed to see status warnings
            return;
        }
        
        $this->shell->exec('sudo', ['/usr/lib/iserv/stsbl_repo_print_umode']);
        $mode = trim(implode('', $this->shell->getOutput()));
        
        if ('testing' === $mode || 'unstable' === $mode) {
            // Inject into IDesk
            $event->addContent(
                'admin.stsblupdatemode',
                'StsblRepositoryMonitorBundle:Dashboard:status.html.twig',
                [
                    'title' => __('%s updates (StsBl repository)', $mode),
                    'icon' => ['style' => 'fugue', 'name' => 'drive-globe'],
                    'text' => __('Your server is currently receiving %s updates from the repository of the Stadtteilschule Blankenese.', $mode),
                    'additional_info' => _('To change that, login as root and run the command stsbl-repoconfig.'),
                    'mode' => $mode,
                    'link' => 'https://it.stsbl.de/documentation/general/update-mode',
                    'link_text' => _('For more information please refer to our documentation'),
                    'panel_class' => 'panel-warning',
                ]
            );
        }
    }

}
