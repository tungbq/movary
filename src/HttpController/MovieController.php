<?php declare(strict_types=1);

namespace Movary\HttpController;

use Movary\Domain\Movie\MovieApi;
use Movary\Domain\Movie\Watchlist\MovieWatchlistApi;
use Movary\Domain\User\Service\Authentication;
use Movary\Domain\User\Service\UserPageAuthorizationChecker;
use Movary\Domain\User\UserApi;
use Movary\Service\Imdb\ImdbMovieRatingSync;
use Movary\Service\Tmdb\SyncMovie;
use Movary\Util\Json;
use Movary\ValueObject\Http\Request;
use Movary\ValueObject\Http\Response;
use Movary\ValueObject\Http\StatusCode;
use Movary\ValueObject\PersonalRating;
use Twig\Environment;

class MovieController
{
    public function __construct(
        private readonly Environment $twig,
        private readonly MovieApi $movieApi,
        private readonly MovieWatchlistApi $movieWatchlistApi,
        private readonly UserApi $userApi,
        private readonly Authentication $authenticationService,
        private readonly UserPageAuthorizationChecker $userPageAuthorizationChecker,
        private readonly SyncMovie $tmdbMovieSync,
        private readonly ImdbMovieRatingSync $imdbMovieRatingSync,
    ) {
    }

    public function addToWatchlist(Request $request) : Response
    {
        if ($this->authenticationService->isUserAuthenticated() === false) {
            return Response::createForbidden();
        }

        $movieId = (int)$request->getRouteParameters()['id'];
        $userId = $this->authenticationService->getCurrentUser()->getId();

        $this->movieWatchlistApi->addMovieToWatchlist($userId, $movieId);

        return Response::createOk();
    }

    public function fetchMovieRatingByTmdbdId(Request $request) : Response
    {
        if ($this->authenticationService->isUserAuthenticated() === false) {
            return Response::createSeeOther('/');
        }

        $userId = $this->authenticationService->getCurrentUserId();
        $tmdbId = $request->getGetParameters()['tmdbId'] ?? null;

        $userRating = null;
        $movie = $this->movieApi->findByTmdbId((int)$tmdbId);

        if ($movie !== null) {
            $userRating = $this->movieApi->findUserRating($movie->getId(), $userId);
        }

        return Response::createJson(
            Json::encode(['personalRating' => $userRating?->asInt()]),
        );
    }

    public function refreshImdbRating(Request $request) : Response
    {
        if ($this->authenticationService->isUserAuthenticated() === false) {
            return Response::createForbidden();
        }

        $movieId = (int)$request->getRouteParameters()['id'];
        $movie = $this->movieApi->findByIdFormatted($movieId);

        if ($movie === null) {
            return Response::createNotFound();
        }

        $this->imdbMovieRatingSync->syncMovieRating($movieId);

        return Response::createOk();
    }

    public function refreshTmdbData(Request $request) : Response
    {
        if ($this->authenticationService->isUserAuthenticated() === false) {
            return Response::createForbidden();
        }

        $movieId = (int)$request->getRouteParameters()['id'];

        $movie = $this->movieApi->findByIdFormatted($movieId);
        if ($movie === null) {
            return Response::createNotFound();
        }

        $tmdbId = $movie['tmdbId'] ?? null;
        if ($tmdbId === null) {
            return Response::createOk();
        }

        $this->tmdbMovieSync->syncMovie($tmdbId);

        return Response::createOk();
    }

    public function removeFromWatchlist(Request $request) : Response
    {
        if ($this->authenticationService->isUserAuthenticated() === false) {
            return Response::createForbidden();
        }

        $movieId = (int)$request->getRouteParameters()['id'];
        $userId = $this->authenticationService->getCurrentUser()->getId();

        $this->movieWatchlistApi->removeMovieFromWatchlist($userId, $movieId);

        return Response::createOk();
    }

    public function renderPage(Request $request) : Response
    {
        $userId = $this->userPageAuthorizationChecker->findUserIdIfCurrentVisitorIsAllowedToSeeUser((string)$request->getRouteParameters()['username']);
        if ($userId === null) {
            return Response::createNotFound();
        }

        $movieId = (int)$request->getRouteParameters()['id'];

        $movie = $this->movieApi->findByIdFormatted($movieId);
        $movie['personalRating'] = $this->movieApi->findUserRating($movieId, $userId)?->asInt();

        return Response::create(
            StatusCode::createOk(),
            $this->twig->render('page/movie.html.twig', [
                'users' => $this->userPageAuthorizationChecker->fetchAllHavingWatchedMovieVisibleUsernamesForCurrentVisitor($movieId),
                'movie' => $movie,
                'movieGenres' => $this->movieApi->findGenresByMovieId($movieId),
                'castMembers' => $this->movieApi->findCastByMovieId($movieId),
                'directors' => $this->movieApi->findDirectorsByMovieId($movieId),
                'totalPlays' => $this->movieApi->fetchHistoryMovieTotalPlays($movieId, $userId),
                'watchDates' => $this->movieApi->fetchHistoryByMovieId($movieId, $userId),
                'isOnWatchlist' => $this->movieWatchlistApi->hasMovieInWatchlist($userId, $movieId),
            ]),
        );
    }

    public function updateRating(Request $request) : Response
    {
        if ($this->authenticationService->isUserAuthenticated() === false) {
            return Response::createForbidden();
        }

        $userId = $this->authenticationService->getCurrentUserId();

        if ($this->userApi->fetchUser($userId)->getName() !== $request->getRouteParameters()['username']) {
            return Response::createForbidden();
        }

        $movieId = (int)$request->getRouteParameters()['id'];

        $postParameters = $request->getPostParameters();

        $personalRating = null;
        if (empty($postParameters['rating']) === false && $postParameters['rating'] !== 0) {
            $personalRating = PersonalRating::create((int)$postParameters['rating']);
        }

        $this->movieApi->updateUserRating($movieId, $this->authenticationService->getCurrentUserId(), $personalRating);

        return Response::create(StatusCode::createNoContent());
    }
}
