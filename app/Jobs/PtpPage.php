<?php

namespace App\Jobs;

use App\PagePtp;
use App\EmailTerminal;
use App\Helpers\CronData;
use App\HistoryShip;
//use App\Mail\PertaminaShipped;
use App\Ship;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class PtpPage implements
    ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $historyShip;
    protected $ship;
    protected $emailTerminal;

    /**
     * Create a new job instance.
     *
     * @param  HistoryShip  $historyShip
     * @param  Ship  $ship
     * @param  array  $emailTerminal
     */
    public function __construct(
        HistoryShip $historyShip,
        Ship $ship,
        array $emailTerminal
    ) {
        $this->historyShip   = $historyShip;
        $this->ship          = $ship;
        $this->emailTerminal = $emailTerminal;
    }

    public function printFloatWithLeadingZeros($num, $precision = 1, $leadingZeros = 3){
        $decimalSeperator = ".";
        $adjustedLeadingZeros = $leadingZeros + mb_strlen($decimalSeperator) + $precision;
        $pattern = "%0{$adjustedLeadingZeros}{$decimalSeperator}{$precision}f";
        return sprintf($pattern,$num);
    }

    private function setFormatPertamina(): string
    {
        foreach (json_decode($this->historyShip->payload)->Fields as $field) {
            $field->Name = strtolower($field->Name);
            if ($field->Name === 'latitude') {
                $latitude = $field->Value;
            }
            if ($field->Name === 'longitude') {
                $longitude = $field->Value;
            }

            if ($field->Name === 'speed') {
                $speed = $field->Value;
            }
            if ($field->Name === 'heading') {
                $heading = $field->Value;
            }

        }

        $latitude  = $this->printFloatWithLeadingZeros(abs((new CronData())->DDtoNme($latitude)), 4, 4).',S';
        $longitude = $this->printFloatWithLeadingZeros(abs((new CronData())->DDtoNme($longitude)), 4, 4).',E';
        $callSign  = $this->ship->call_sign ?? 'null';
        return '"'.$callSign.'","'.$this->ship->name.'","$SKYSATU,'.date('His',
                strtotime($this->historyShip->message_utc)).',A,'.$latitude.','.$longitude.','.$this->printFloatWithLeadingZeros($speed).','.$this->printFloatWithLeadingZeros($heading).','.date('dmy').',000.0,E*68"';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->ship->additional_email_ship != null) {
            $this->emailTerminal = explode(';', $this->ship->additional_email_ship);
        }

        $data                        = new PagePtp();
        $data->ship_id               = $this->ship->id;
        $data->history_ship_id       = $this->historyShip->id;
        $data->last_sent_destination = json_encode($this->emailTerminal);
        $data->subject               = $this->ship->name.'-'.date('dmY-Hi',strtotime($this->historyShip->message_utc));
        $data->filename_chr          = $this->ship->name.'-'.date('dmY-Hi', strtotime($this->historyShip->message_utc)).'.chr';
        $data->content               = $this->setFormatPertamina();

        $data->save();
    }
}
