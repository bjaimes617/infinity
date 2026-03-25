<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class EmailCondomios extends Notification
{
    use Queueable;
    
     protected $data;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
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
                ->subject('Reporte de Visitantes de Directa Group')
                ->greeting("Un gusto en saludarle! ".$this->data['namecliente'])   
                ->line(new HtmlString("<div align='justify'>Por medio de la presente te hacemos llegar el reporte de visitantes registrado que corresponde al día ".date('d/m/Y', strtotime($this->data['fecha'])).".</div><br>"))                                
                ->line(new HtmlString("<div align='justify'>Importante mencionarle que este es un correo automatico y no debe responder al remitente. Cualquier duda puede escribirnos a traves de <a href='mailto:'>innovacion@directagroup.net</a> </div><br>"))                                
                ->salutation('Atentamente, Directa Group C.A')->attach(storage_path('app/condominios/'.$this->data['namefile']));   
                
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
