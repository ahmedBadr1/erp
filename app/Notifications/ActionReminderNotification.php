<?php

namespace App\Notifications;

use App\Models\Action;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ActionReminderNotification extends Notification
{
    use Queueable;

    private $action ;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Action $action)
    {
       $this->action = $action;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail','database'];
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
                    ->line('The introduction to the notification.')
                    ->line('New Action Is due Today')
                    ->action('See Action',route('crm.actions.show',$this->action->id))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'from' => config('app.name'),
            'msg'=> 'Action Reminder',
            'url' => '/setting',
        ];
    }
}
