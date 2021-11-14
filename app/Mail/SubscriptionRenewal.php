<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubscriptionRenewal extends Mailable
{
    use Queueable, SerializesModels;

    public $subscriptionType;
    public $pathToFile;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subscriptionType, $pathToFile)
    {
        $this->subscriptionType = $subscriptionType;
        $this->pathToFile = $pathToFile;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if($this->subscriptionType->slug == 'hosting')
            $subject = 'Odnowienie hostingu';
        if($this->subscriptionType->slug == 'domain')
            $subject = 'Odnowienie domeny';

        return $this->subject($subject)
            ->markdown('email.subscription-renewal')
            ->attach(storage_path('app/'.$this->pathToFile));
    }
}
