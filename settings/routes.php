<?php declare(strict_types=1);

return static function (FastRoute\RouteCollector $routeCollector) {
    $routeCollector->addRoute(
        'GET',
        '/',
        [\Movary\HttpController\LandingPageController::class, 'render'],
    );
    $routeCollector->addRoute(
        'POST',
        '/login',
        [\Movary\HttpController\AuthenticationController::class, 'login'],
    );
    $routeCollector->addRoute(
        'GET',
        '/login',
        [\Movary\HttpController\AuthenticationController::class, 'renderLoginPage'],
    );
    $routeCollector->addRoute(
        'GET',
        '/logout',
        [\Movary\HttpController\AuthenticationController::class, 'logout'],
    );
    $routeCollector->addRoute(
        'POST',
        '/create-user',
        [\Movary\HttpController\CreateUserController::class, 'createUser'],
    );
    $routeCollector->addRoute(
        'GET',
        '/create-user',
        [\Movary\HttpController\CreateUserController::class, 'renderPage'],
    );

    #####################
    # Webhook listeners #
    #####################
    $routeCollector->addRoute(
        'POST',
        '/plex/{id:.+}',
        [\Movary\HttpController\PlexController::class, 'handlePlexWebhook'],
    );
    $routeCollector->addRoute(
        'POST',
        '/jellyfin/{id:.+}',
        [\Movary\HttpController\JellyfinController::class, 'handleJellyfinWebhook'],
    );
    $routeCollector->addRoute(
        'POST',
        '/emby/{id:.+}',
        [\Movary\HttpController\EmbyController::class, 'handleEmbyWebhook'],
    );

    #############
    # Job Queue #
    #############
    $routeCollector->addRoute(
        'GET',
        '/job-queue',
        [\Movary\HttpController\JobController::class, 'renderQueuePage'],
    );
    $routeCollector->addRoute(
        'GET',
        '/job-queue/purge-processed',
        [\Movary\HttpController\JobController::class, 'purgeProcessedJobs'],
    );
    $routeCollector->addRoute(
        'GET',
        '/job-queue/purge-all',
        [\Movary\HttpController\JobController::class, 'purgeAllJobs'],
    );
    $routeCollector->addRoute(
        'GET',
        '/jobs/schedule/trakt-history-sync',
        [\Movary\HttpController\JobController::class, 'scheduleTraktHistorySync'],
    );
    $routeCollector->addRoute(
        'GET',
        '/jobs/schedule/trakt-ratings-sync',
        [\Movary\HttpController\JobController::class, 'scheduleTraktRatingsSync'],
    );
    $routeCollector->addRoute(
        'POST',
        '/jobs/schedule/letterboxd-diary-sync',
        [\Movary\HttpController\JobController::class, 'scheduleLetterboxdDiaryImport'],
    );
    $routeCollector->addRoute(
        'POST',
        '/jobs/schedule/letterboxd-ratings-sync',
        [\Movary\HttpController\JobController::class, 'scheduleLetterboxdRatingsImport'],
    );

    ############
    # Settings #
    ############
    $routeCollector->addRoute(
        'GET',
        '/settings/account/general',
        [\Movary\HttpController\SettingsController::class, 'renderGeneralAccountPage'],
    );
    $routeCollector->addRoute(
        'GET',
        '/settings/account/password',
        [\Movary\HttpController\SettingsController::class, 'renderPasswordAccountPage'],
    );
    $routeCollector->addRoute(
        'GET',
        '/settings/account/data',
        [\Movary\HttpController\SettingsController::class, 'renderDataAccountPage'],
    );
    $routeCollector->addRoute(
        'GET',
        '/settings/server/general',
        [\Movary\HttpController\SettingsController::class, 'renderServerGeneralPage'],
    );
    $routeCollector->addRoute(
        'POST',
        '/settings/server/general',
        [\Movary\HttpController\SettingsController::class, 'updateServerGeneral'],
    );
    $routeCollector->addRoute(
        'GET',
        '/settings/server/users',
        [\Movary\HttpController\SettingsController::class, 'renderServerUsersPage'],
    );
    $routeCollector->addRoute(
        'POST',
        '/settings/account',
        [\Movary\HttpController\SettingsController::class, 'updateGeneral'],
    );
    $routeCollector->addRoute(
        'POST',
        '/settings/account/password',
        [\Movary\HttpController\SettingsController::class, 'updatePassword'],
    );
    $routeCollector->addRoute(
        'GET',
        '/settings/account/export/csv/{exportType:.+}',
        [\Movary\HttpController\ExportController::class, 'getCsvExport'],
    );
    $routeCollector->addRoute(
        'POST',
        '/settings/account/import/csv/{exportType:.+}',
        [\Movary\HttpController\ImportController::class, 'handleCsvImport'],
    );
    $routeCollector->addRoute(
        'GET',
        '/settings/account/delete-ratings',
        [\Movary\HttpController\SettingsController::class, 'deleteRatings'],
    );
    $routeCollector->addRoute(
        'GET',
        '/settings/account/delete-history',
        [\Movary\HttpController\SettingsController::class, 'deleteHistory'],
    );
    $routeCollector->addRoute(
        'GET',
        '/settings/account/delete-account',
        [\Movary\HttpController\SettingsController::class, 'deleteAccount'],
    );
    $routeCollector->addRoute(
        'GET',
        '/settings/integrations/trakt',
        [\Movary\HttpController\SettingsController::class, 'renderTraktPage'],
    );
    $routeCollector->addRoute(
        'POST',
        '/settings/trakt',
        [\Movary\HttpController\SettingsController::class, 'updateTrakt'],
    );
    $routeCollector->addRoute(
        'POST',
        '/settings/trakt/verify-credentials',
        [\Movary\HttpController\SettingsController::class, 'traktVerifyCredentials'],
    );
    $routeCollector->addRoute(
        'GET',
        '/settings/integrations/letterboxd',
        [\Movary\HttpController\SettingsController::class, 'renderLetterboxdPage'],
    );
    $routeCollector->addRoute(
        'GET',
        '/settings/letterboxd-export',
        [\Movary\HttpController\SettingsController::class, 'generateLetterboxdExportData'],
    );
    $routeCollector->addRoute(
        'GET',
        '/settings/integrations/plex',
        [\Movary\HttpController\SettingsController::class, 'renderPlexPage'],
    );
    $routeCollector->addRoute(
        'POST',
        '/settings/plex',
        [\Movary\HttpController\SettingsController::class, 'updatePlex'],
    );
    $routeCollector->addRoute(
        'PUT',
        '/settings/plex/webhook',
        [\Movary\HttpController\PlexController::class, 'regeneratePlexWebhookUrl'],
    );
    $routeCollector->addRoute(
        'DELETE',
        '/settings/plex/webhook',
        [\Movary\HttpController\PlexController::class, 'deletePlexWebhookUrl'],
    );
    $routeCollector->addRoute(
        'GET',
        '/settings/integrations/jellyfin',
        [\Movary\HttpController\SettingsController::class, 'renderJellyfinPage'],
    );
    $routeCollector->addRoute(
        'POST',
        '/settings/jellyfin',
        [\Movary\HttpController\SettingsController::class, 'updateJellyfin'],
    );
    $routeCollector->addRoute(
        'GET',
        '/settings/jellyfin/webhook',
        [\Movary\HttpController\JellyfinController::class, 'getJellyfinWebhookUrl'],
    );
    $routeCollector->addRoute(
        'PUT',
        '/settings/jellyfin/webhook',
        [\Movary\HttpController\JellyfinController::class, 'regenerateJellyfinWebhookUrl'],
    );
    $routeCollector->addRoute(
        'DELETE',
        '/settings/jellyfin/webhook',
        [\Movary\HttpController\JellyfinController::class, 'deleteJellyfinWebhookUrl'],
    );
    $routeCollector->addRoute(
        'GET',
        '/settings/integrations/emby',
        [\Movary\HttpController\SettingsController::class, 'renderEmbyPage'],
    );
    $routeCollector->addRoute(
        'POST',
        '/settings/emby',
        [\Movary\HttpController\SettingsController::class, 'updateEmby'],
    );
    $routeCollector->addRoute(
        'GET',
        '/settings/emby/webhook',
        [\Movary\HttpController\EmbyController::class, 'getEmbyWebhookUrl'],
    );
    $routeCollector->addRoute(
        'PUT',
        '/settings/emby/webhook',
        [\Movary\HttpController\EmbyController::class, 'regenerateEmbyWebhookUrl'],
    );
    $routeCollector->addRoute(
        'DELETE',
        '/settings/emby/webhook',
        [\Movary\HttpController\EmbyController::class, 'deleteEmbyWebhookUrl'],
    );
    $routeCollector->addRoute(
        'GET',
        '/settings/app',
        [\Movary\HttpController\SettingsController::class, 'renderAppPage'],
    );
    $routeCollector->addRoute(
        'GET',
        '/settings/integrations/netflix',
        [\Movary\HttpController\SettingsController::class, 'renderNetflixPage'],
    );
    $routeCollector->addRoute(
        'POST',
        '/settings/netflix',
        [\Movary\HttpController\NetflixController::class, 'matchNetflixActivityCsvWithTmdbMovies'],
    );
    $routeCollector->addRoute(
        'POST',
        '/settings/netflix/import',
        [\Movary\HttpController\NetflixController::class, 'importNetflixData'],
    );
    $routeCollector->addRoute(
        'POST',
        '/settings/netflix/search',
        [\Movary\HttpController\NetflixController::class, 'searchTmbd'],
    );

    ##########
    # Movies #
    ##########
    $routeCollector->addRoute(
        'GET',
        '/movies/{id:[0-9]+}/refresh-tmdb',
        [\Movary\HttpController\MovieController::class, 'refreshTmdbData'],
    );
    $routeCollector->addRoute(
        'GET',
        '/movies/{id:[0-9]+}/refresh-imdb',
        [\Movary\HttpController\MovieController::class, 'refreshImdbRating'],
    );
    $routeCollector->addRoute(
        'GET',
        '/movies/{id:[0-9]+}/add-watchlist',
        [\Movary\HttpController\MovieController::class, 'addToWatchlist'],
    );
    $routeCollector->addRoute(
        'GET',
        '/movies/{id:[0-9]+}/remove-watchlist',
        [\Movary\HttpController\MovieController::class, 'removeFromWatchlist'],
    );

    ##############
    # User media #
    ##############
    $routeCollector->addRoute(
        'GET',
        '/users/{username:[a-zA-Z0-9]+}/dashboard',
        [\Movary\HttpController\DashboardController::class, 'render'],
    );
    $routeCollector->addRoute(
        'GET',
        '/users/{username:[a-zA-Z0-9]+}/history',
        [\Movary\HttpController\HistoryController::class, 'renderHistory'],
    );
    $routeCollector->addRoute(
        'GET',
        '/users/{username:[a-zA-Z0-9]+}/watchlist',
        [\Movary\HttpController\WatchlistController::class, 'renderWatchlist'],
    );
    $routeCollector->addRoute(
        'GET',
        '/users/{username:[a-zA-Z0-9]+}/movies',
        [\Movary\HttpController\MoviesController::class, 'renderPage'],
    );
    $routeCollector->addRoute(
        'GET',
        '/users/{username:[a-zA-Z0-9]+}/actors',
        [\Movary\HttpController\ActorsController::class, 'renderPage'],
    );
    $routeCollector->addRoute(
        'GET',
        '/users/{username:[a-zA-Z0-9]+}/directors',
        [\Movary\HttpController\DirectorsController::class, 'renderPage'],
    );
    $routeCollector->addRoute(
        'GET',
        '/users/{username:[a-zA-Z0-9]+}/movies/{id:\d+}',
        [\Movary\HttpController\MovieController::class, 'renderPage'],
    );
    $routeCollector->addRoute(
        'GET',
        '/users/{username:[a-zA-Z0-9]+}/persons/{id:\d+}',
        [\Movary\HttpController\PersonController::class, 'renderPage'],
    );
    $routeCollector->addRoute(
        'DELETE',
        '/users/{username:[a-zA-Z0-9]+}/movies/{id:\d+}/history',
        [\Movary\HttpController\HistoryController::class, 'deleteHistoryEntry'],
    );
    $routeCollector->addRoute(
        'POST',
        '/users/{username:[a-zA-Z0-9]+}/movies/{id:\d+}/history',
        [\Movary\HttpController\HistoryController::class, 'createHistoryEntry'],
    );
    $routeCollector->addRoute(
        'POST',
        '/users/{username:[a-zA-Z0-9]+}/movies/{id:\d+}/rating',
        [\Movary\HttpController\MovieController::class, 'updateRating'],
    );
    $routeCollector->addRoute(
        'POST',
        '/log-movie',
        [\Movary\HttpController\HistoryController::class, 'logMovie'],
    );
    $routeCollector->addRoute(
        'POST',
        '/add-movie-to-watchlist',
        [\Movary\HttpController\WatchlistController::class, 'addMovieToWatchlist'],
    );
    $routeCollector->addRoute(
        'GET',
        '/fetchMovieRatingByTmdbdId',
        [\Movary\HttpController\MovieController::class, 'fetchMovieRatingByTmdbdId'],
    );

    // Added last, so that more specific routes can be defined (possible username vs route collisions here!)
    $routeCollector->addRoute(
        'GET',
        '/{username:[a-zA-Z0-9]+}[/]',
        [\Movary\HttpController\DashboardController::class, 'redirectToDashboard'],
    );

    ############
    # REST Api #
    ############
    $routeCollector->addRoute(
        'GET',
        '/api/users',
        [\Movary\HttpController\Rest\UserController::class, 'fetchUsers'],
    );
    $routeCollector->addRoute(
        'POST',
        '/api/users',
        [\Movary\HttpController\Rest\UserController::class, 'createUser'],
    );
    $routeCollector->addRoute(
        'PUT',
        '/api/users/{userId:\d+}',
        [\Movary\HttpController\Rest\UserController::class, 'updateUser'],
    );
    $routeCollector->addRoute(
        'DELETE',
        '/api/users/{userId:\d+}',
        [\Movary\HttpController\Rest\UserController::class, 'deleteUser'],
    );
};
