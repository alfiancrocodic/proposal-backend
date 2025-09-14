<?php

namespace App\Providers;

use App\Models\Client;
use App\Policies\ClientPolicy;
use App\Models\Project;
use App\Policies\ProjectPolicy;
use App\Models\Proposal;
use App\Policies\ProposalPolicy;
use App\Models\ProposalContent;
use App\Policies\ProposalContentPolicy;
use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Client::class, ClientPolicy::class);
        Gate::policy(Project::class, ProjectPolicy::class);
        Gate::policy(Proposal::class, ProposalPolicy::class);
        Gate::policy(ProposalContent::class, ProposalContentPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
    }
}
