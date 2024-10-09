<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TransactionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $transaction;
    protected $recipient;
    protected $sender;


    /**
     * Create a new notification instance.
     */
    public function __construct($transaction, $recipient, $sender)
    {
        $this->transaction = $transaction;
        $this->recipient = $recipient;
        $this->sender = $sender;

        $this->storeInDatabase();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Transaction Notification')
            ->line('You have a new transaction.')
            ->line('Amount: ' . $this->transaction->amount)
            ->line('Sender: ' . $this->sender->name)
            ->line('Recipient: ' . $this->recipient->name)
            ->line('Thank you for using our service!');
    }

    public function storeInDatabase()
    {
        TransactionNotification::create([
            'transaction_id' => $this->transaction->id,
            'title' => 'Transaction Notification',
            'message' => 'Transaction of ' . $this->transaction->amount . ' from ' . $this->sender->name . ' to ' . $this->recipient->name,
        ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
