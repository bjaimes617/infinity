<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\HtmlString;


class NewUser extends Notification
{
    use Queueable;
    
    protected $data;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(array $arr)
    {
        $this->data = $arr;
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
    public function toMail($notifiable)
    {
        return (new MailMessage)
                ->subject('Tu cuenta en '.ENV('APP_NAME').' esta lista!')
                ->greeting("Hola! ".$this->data['name']." Bienvenido al equipo!")   
                ->line(new HtmlString("<div align='justify'>Su cuenta de usuario se ha creado correctamente. Enviamos el nombre de usuario y contrase&ntilde;a, recuerde cambiar la contrase&ntilde;a por una segura y f&aacute;cil de recordar.</div><br>"))
                ->line(new HtmlString("<b>Usuario</b>: ".$this->data['username']))
                ->line(new HtmlString("<b>Password</b>: ".$this->data['password']))
                ->action('¡Conectate...!', url('/'))
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
