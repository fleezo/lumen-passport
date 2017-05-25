<?php

namespace Dusterio\LumenPassport;

class RouteRegistrar
{
    /**
     * Application
     */
    private $app;

    /**
     * Create a new route registrar instance.
     *
     * @param  $app
     * @return void
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Register routes for transient tokens, clients, and personal access tokens.
     *
     * @return void
     */
    public function all()
    {
        $this->forAccessTokens();
        $this->forTransientTokens();
        $this->forClients();
        $this->forPersonalAccessTokens();
    }

    /**
     * Register the routes for retrieving and issuing access tokens.
     *
     * @return void
     */
    public function forAccessTokens()
    {
		$this->app->group(['namespace' => '\Dusterio\LumenPassport\Http\Controllers'], function () {
			$this->app->post('/token', [
	            'uses' => 'AccessTokenController@issueToken'
	        ]);
		});

        $this->app->group(['namespace' => '\Laravel\Passport\Http\Controllers', 'middleware' => ['auth']], function () {
            $this->app->get('/tokens', [
                'uses' => 'AuthorizedAccessTokenController@forUser'
            ]);

            $this->app->delete('/tokens/{token_id}', [
                'uses' => 'AuthorizedAccessTokenController@destroy'
            ]);
        });
    }

    /**
     * Register the routes needed for refreshing transient tokens.
     *
     * @return void
     */
    public function forTransientTokens()
    {
		$this->app->group(['namespace' => '\Laravel\Passport\Http\Controllers', 'middleware' => ['auth']], function () {
	        $this->app->post('/token/refresh', [
	            'uses' => 'TransientTokenController@refresh'
	        ]);
		});
    }

    /**
     * Register the routes needed for managing clients.
     *
     * @return void
     */
    public function forClients()
    {
		$this->app->group(['namespace' => '\Laravel\Passport\Http\Controllers', 'middleware' => ['auth']], function () {
            $this->app->get('/clients', [
                'uses' => 'ClientController@forUser'
            ]);

            $this->app->post('/clients', [
                'uses' => 'ClientController@store'
            ]);

            $this->app->put('/clients/{client_id}', [
                'uses' => 'ClientController@update'
            ]);

            $this->app->delete('/clients/{client_id}', [
                'uses' => 'ClientController@destroy'
            ]);
        });
    }

    /**
     * Register the routes needed for managing personal access tokens.
     *
     * @return void
     */
    public function forPersonalAccessTokens()
    {
		$this->app->group(['namespace' => '\Laravel\Passport\Http\Controllers', 'middleware' => ['auth']], function () {
            $this->app->get('/scopes', [
                'uses' => 'ScopeController@all'
            ]);

            $this->app->get('/personal-access-tokens', [
                'uses' => 'PersonalAccessTokenController@forUser'
            ]);

            $this->app->post('/personal-access-tokens', [
                'uses' => 'PersonalAccessTokenController@store'
            ]);

            $this->app->delete('/personal-access-tokens/{token_id}', [
                'uses' => 'PersonalAccessTokenController@destroy'
            ]);
        });
    }
}
