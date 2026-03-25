<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\HtmlString;


class PasswordReset extends Notification
{
    use Queueable;

    public $token;
    
    public $user;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
     public function __construct($token,$user)
    {
        $this->token = $token;
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable){
        
        return (new MailMessage)
                ->subject(ENV('APP_NAME').' Recuperación de contraseña')
                ->greeting("Hola! ".$this->user->name)   
                ->line(new HtmlString("<div align='justify'>Has solicitado el Reinicio de tu contraseña de usuario en nuestra plataforma.</div><br>"))              
                ->line(new HtmlString("<div align='justify'>Haz click en el siguiente boton, para iniciar el proceso de restablecimiento de contraseña</div>"))
                ->action('Recuperar Contraseña', route('password.reset.token',[$this->user->email,$this->token]))
                ->salutation('Atentamente, Directa Group C.A');    
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
