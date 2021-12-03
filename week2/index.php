<?php
/**
 * Controller
 *
 * Database-driven Webtechnology
 * Taught by Stijn Eikelboom
 * Based on code by Reinard van Dalen
 */

include 'model.php';

/* Connect to DB */
$db = connect_db('localhost', 'ddwt21_week2', 'ddwt21','ddwt21');

$nbr_series = count_series($db);
$nbr_users = count_users($db);

$right_column = use_template('cards');
session_start();

$nav_template = Array(
    1 => Array(
        'name' => 'Home',
        'url' => '/DDWT21/week2/'
    ),
    2 => Array(
        'name' => 'Overview',
        'url' => '/DDWT21/week2/overview/'
    ),
    3 => Array(
        'name' => 'Add Series',
        'url' => '/DDWT21/week2/add/'
    ),
    4 => Array(
        'name' => 'My Account',
        'url' => '/DDWT21/week2/myaccount/'
    ),
    5 => Array(
        'name' => 'Register',
        'url' => '/DDWT21/week2/register/'
    )
);

/* Landing page */
if (new_route('/DDWT21/week2/', 'get')) {
    /* Page info */
    $page_title = 'Home';
    $breadcrumbs = get_breadcrumbs([
        'DDWT21' => na('/DDWT21/', False),
        'Week 2' => na('/DDWT21/week2/', False),
        'Home' => na('/DDWT21/week2/', True),
    ]);
    $navigation = get_navigation($nav_template, 1);

    /* Page content */
    $page_subtitle = 'The online platform to list your favorite series';
    $page_content = 'On Series Overview you can list your favorite series. You can see the favorite series of all Series Overview users. By sharing your favorite series, you can get inspired by others and explore new series.';

    /* Choose Template */
    include use_template('main');
}

/* Overview page */
elseif (new_route('/DDWT21/week2/overview/', 'get')) {
    /* Page info */
    $page_title = 'Overview';
    $breadcrumbs = get_breadcrumbs([
        'DDWT21' => na('/DDWT21/', False),
        'Week 2' => na('/DDWT21/week2/', False),
        'Overview' => na('/DDWT21/week2/overview', True),
    ]);
    $navigation = get_navigation($nav_template, 2);

    /* Page content */
    $page_subtitle = 'The overview of all series';
    $page_content = 'Here you find all series listed on Series Overview.';
    $left_content = get_series_table($db, get_series($db));

    /* Choose Template */
    include use_template('main');
}

/* Single Series */
elseif (new_route('/DDWT21/week2/series/', 'get')) {
    /* Get series from db */
    $series_id = $_GET['series_id'];
    $series_info = get_series_info($db, $series_id);

    /* Page info */
    $page_title = $series_info['name'];
    $breadcrumbs = get_breadcrumbs([
        'DDWT21' => na('/DDWT21/', False),
        'Week 2' => na('/DDWT21/week2/', False),
        'Overview' => na('/DDWT21/week2/overview/', False),
        $series_info['name'] => na('/DDWT21/week2/series/?series_id='.$series_id, True)
    ]);
    $navigation = get_navigation($nav_template, 3);

    /* Page content */
    $page_subtitle = sprintf("Information about %s", $series_info['name']);
    $page_content = $series_info['abstract'];
    $nbr_seasons = $series_info['seasons'];
    $creators = $series_info['creator'];
    $added_by = get_name($db, $series_info['user']);

    /* Choose Template */
    include use_template('series');
}

/* Add series GET */
elseif (new_route('/DDWT21/week2/add/', 'get')) {
    /* Page info */
    $page_title = 'Add Series';
    $breadcrumbs = get_breadcrumbs([
        'DDWT21' => na('/DDWT21/', False),
        'Week 2' => na('/DDWT21/week2/', False),
        'Add Series' => na('/DDWT21/week2/new/', True)
    ]);
    $navigation = get_navigation($nav_template, 3);

    /* Page content */
    $page_subtitle = 'Add your favorite series';
    $page_content = 'Fill in the details of you favorite series.';
    $submit_btn = "Add Series";
    $form_action = '/DDWT21/week2/add/';

    if ( isset($_GET['error_msg']) ) { $error_msg = get_error($_GET['error_msg']); }

    /* Choose Template */
    include use_template('new');
}

/* Add series POST */
elseif (new_route('/DDWT21/week2/add/', 'post')) {
    /* Add series to database */
    $feedback = add_series($db, $_POST);
    $error_msg = get_error($feedback);

    $_GET['error_msg'] = $error_msg;
    redirect('/DDWT21/week2/add/');

    include use_template('new');
}

/* Edit series GET */
elseif (new_route('/DDWT21/week2/edit/', 'get')) {
    /* Get series info from db */
    $series_id = $_GET['series_id'];
    $series_info = get_series_info($db, $series_id);

    /* Page info */
    $page_title = 'Edit Series';
    $breadcrumbs = get_breadcrumbs([
        'DDWT21' => na('/DDWT21/', False),
        'Week 2' => na('/DDWT21/week2/', False),
        sprintf("Edit Series %s", $series_info['name']) => na('/DDWT21/week2/new/', True)
    ]);
    $navigation = get_navigation($nav_template, 3);

    /* Page content */
    $page_subtitle = sprintf("Edit %s", $series_info['name']);
    $page_content = 'Edit the series below.';
    $submit_btn = "Edit Series";
    $form_action = '/DDWT21/week2/edit/';

    /* Choose Template */
    include use_template('new');
}

/* Edit series POST */
elseif (new_route('/DDWT21/week2/edit/', 'post')) {
    /* Update series in database */
    $feedback = update_series($db, $_POST);
    $error_msg = get_error($feedback);

    /* Get series info from db */
    $series_id = $_POST['series_id'];
    $series_info = get_series_info($db, $series_id);

    $_GET['error_msg'] = $error_msg;
    $_GET['series_id'] = $series_id;
    redirect('/DDWT21/week2/series/');

    /* Choose Template */
    include use_template('series');
}

/* Remove series */
elseif (new_route('/DDWT21/week2/remove/', 'post')) {
    /* Remove series in database */
    $series_id = $_POST['series_id'];
    $feedback = remove_series($db, $series_id);
    $error_msg = get_error($feedback);

    /* Page info */
    $page_title = 'Overview';
    $breadcrumbs = get_breadcrumbs([
        'DDWT21' => na('/DDWT21/', False),
        'Week 2' => na('/DDWT21/week2/', False),
        'Overview' => na('/DDWT21/week2/overview', True)
    ]);
    $navigation = get_navigation($nav_template, 2);

    /* Page content */
    $page_subtitle = 'The overview of all series';
    $page_content = 'Here you find all series listed on Series Overview.';
    $left_content = get_series_table($db, get_series($db));

    $_GET['error_msg'] = $error_msg;
    redirect('/DDWT21/week2/overview/');

    /* Choose Template */
    include use_template('main');
}

elseif (new_route('/DDWT21/week2/myaccount/', 'get')) {
    /* Redirect to login */
    if(!check_login()){
        redirect('/DDWT21/week2/login');}

    /* Page info */
    $page_title = 'My Account';
    $breadcrumbs = get_breadcrumbs([
        'DDWT21' => na('/DDWT21/', False),
        'Week 2' => na('/DDWT21/week2/', False),
        'My Account' => na('/DDWT21/week2/myaccount/', True)
    ]);

    /* Page content */
    $user = get_name($db, $_SESSION['user_id']);
    $navigation = get_navigation($nav_template, 4);
    $page_subtitle = 'An overview of your user account';
    $page_content = 'Here you find your personal Series Overview account.';

    if ( isset($_GET['error_msg']) ) { $error_msg = get_error($_GET['error_msg']); }

    /* Choose Template */
    include use_template('account');
}

elseif (new_route('/DDWT21/week2/register/', 'get')) {
    /* Page info */
    $page_title = 'Register';
    $breadcrumbs = get_breadcrumbs([
        'DDWT21' => na('/DDWT21/', False),
        'Week 2' => na('/DDWT21/week2/', False),
        'Register' => na('/DDWT21/week2/register/', True)
    ]);

    /* Page content */
    $navigation = get_navigation($nav_template, 5);
    $page_subtitle = 'Register for Series Overview';
    $page_content = 'Here you can create your Series Overview account.';

    if (isset($_GET['error_msg'])) {
        $error_msg = get_error($_GET['error_msg']);
    }

    /* Choose Template */
    include use_template('register');
}

elseif (new_route('/DDWT21/week2/register/', 'post')) {
    /* Register user */
    $feedback = register_user($db, $_POST);
    if($feedback['type'] == 'error') {
        /* Redirect to register form */
        redirect(sprintf("/DDWT21/week2/register/?error_msg=%s",
                 json_encode($feedback)));
    } else {
        /* Redirect to My Account page */
        redirect(sprintf("/DDWT21/week2/myaccount/?error_msg=%s",
            json_encode($feedback)));
    }

    /* Choose Template */
    include use_template('register');
}

elseif (new_route('/DDWT21/week2/login/', 'get')) {
    /* Page info */
    $page_title = 'Login';
    $breadcrumbs = get_breadcrumbs([
        'DDWT21' => na('/DDWT21/', False),
        'Week 2' => na('/DDWT21/week2/', False),
        'Login' => na('/DDWT21/week2/login', True)
    ]);

    /* Page content */
    $navigation = get_navigation($nav_template, 4);
    $page_subtitle = 'Login to your Series Overview account';
    $page_content = 'Here you can login to your Series Overview account.';

    if (isset($_GET['error_msg'])) {
        $error_msg = get_error($_GET['error_msg']);
    }

    /* Choose Template */
    include use_template('login');
}

elseif (new_route('/DDWT21/week2/login/', 'post')) {
    $feedback = login_user($db, $_POST);
    if ($feedback['type'] == 'error') {
        /* Redirect to register form */
        redirect(sprintf("/DDWT21/week2/register/?error_msg=%s",
            json_encode($feedback)));
    } else {
        /* Redirect to My Account page */
        redirect(sprintf("/DDWT21/week2/myaccount/?error_msg=%s",
            json_encode($feedback)));

        if (isset($_GET['error_msg'])) {
            $error_msg = get_error($_GET['error_msg']);
        }

        /* Choose Template */
        include use_template('login');
    }
}

elseif (new_route('/DDWT21/week2/logout/', 'get')) {
    $feedback = logout_user();
    redirect(sprintf("/DDWT21/week2/overview/?error_msg=%s",
        json_encode($feedback)));
}

else {
    http_response_code(404);
    echo '404 Not Found';
}


