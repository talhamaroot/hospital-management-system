<?php

namespace App\Filament\Pages\Auth;

use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Pages\Auth\Login as BasePage;
use Illuminate\Contracts\Support\Htmlable;

class Login extends BasePage
{
    public function mount(): void
    {
        parent::mount();

    }

    public function getHeading(): string|Htmlable
    {
        return Filament::getCurrentPanel()->getId() === "admin" ? 'Login as Admin' : 'Login as Receptionist';
    }

    protected function getFormActions(): array
    {

        return [
            $this->getAuthenticateFormAction(),
              Action::make('register')
                 ->link( )
                  ->action(fn () => Filament::getCurrentPanel()->getId() === "admin" ? $this->redirect('/receptionist') : $this->redirect('/'))
                 ->label(Filament::getCurrentPanel()->getId() === "admin" ? 'Login as Receptionist' : 'Login as admin')
                 ->url(filament()->getRegistrationUrl())
        ];
    }
}
