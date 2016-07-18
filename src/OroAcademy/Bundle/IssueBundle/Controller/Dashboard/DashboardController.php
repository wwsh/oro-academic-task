<?php
/*******************************************************************************
 * This is closed source software, created by WWSH. 
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016. 
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Controller\Dashboard;

use FOS\RestBundle\Controller\Annotations\Route;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DashboardController extends Controller
{
    /**
     * @param $widget
     * @return array
     *
     * @Route("/widget/issueChart/{widget}",
     *     name="oroacademy_issues_by_status_widget",
     *     requirements={"widget"="[\w-]+"}
     *     )
     * @Template("OroAcademyIssueBundle:Dashboard:issuesByStatus.html.twig")
     * @AclAncestor("oroacademy_view_issue")
     */
    public function issuesByStatusAction($widget)
    {
        $items = $this->getDoctrine()
            ->getRepository('OroAcademyIssueBundle:Issue')
            ->getIssuesByStatus();

        $widgetAttr = $this->get('oro_dashboard.widget_configs')
            ->getWidgetAttributesForTwig($widget);

        $widgetAttr['chartView'] = $this->get('oro_chart.view_builder')
            ->setArrayData($items)
            ->setOptions(
                [
                    'name'        => 'bar_chart',
                    'data_schema' => [
                        'label' => [ 'field_name' => 'label' ],
                        'value' => [ 'field_name' => 'number' ]
                    ],
                    'settings'    => [ 'xNoTicks' => count($items) - 1 ]
                ]
            )
            ->getView();

        return $widgetAttr;
    }

    /**
     * @Route("/widget/myIssues/{widget}",
     *     name="oroacademy_my_active_issues",
     *     requirements={"widget"="[\w-]+"}
     *     )
     * @Template("OroAcademyIssueBundle:Dashboard:activeIssues.html.twig")
     * @AclAncestor("oroacademy_view_issue")
     */
    public function myActiveIssues($widget)
    {
        $widgetAttr = $this->get('oro_dashboard.widget_configs')
            ->getWidgetAttributesForTwig($widget);

        return $widgetAttr;
    }
}
