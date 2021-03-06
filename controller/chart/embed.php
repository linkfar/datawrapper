<?php

/*
 * Main controller for chart rendering
 */
$app->get('/chart/:id/', function ($id) use ($app) {
    disable_cache($app);


    check_chart_public($id, function($user, $chart) use ($app) {
        if ($chart->getLanguage() != '') {
            global $__l10n;
            $__l10n->loadMessages($chart->getLanguage());
        }

        $theme = (empty($app->request()->get('theme')) ? $chart->getTheme() : $app->request()->get('theme'));
        $theme = ThemeQuery::create()->findPk($theme);
        if (empty($theme)) $theme = ThemeQuery::create()->findPk("default");
        $page['theme'] = $theme;

        $page = get_chart_content($chart, $user, $theme, $app->request()->get('minify'), $app->request()->get('debug'));
        $page['thumb'] = $app->request()->params('t') == 1;
        $page['innersvg'] = $app->request()->get('innersvg') == 1;
        $page['plain'] = $app->request()->get('plain') == 1;
        $page['fullscreen'] = $app->request()->get('fs') == 1;

        if (!empty($GLOBALS['dw_config']['prevent_chart_preview_in_iframes'])) {
            // prevent this url from being rendered in iframes on different
            // domains, mainly to protect server resources
            $res = $app->response();
            $res['X-Frame-Options'] = 'SAMEORIGIN';
        }
        $app->render('chart.twig', $page);
    });
});

$app->get('/chart/:id', function($id) use ($app) {
    $app->redirect('/chart/' . $id . '/');
});
