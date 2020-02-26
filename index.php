<?php

trait EngineTrait
{
    protected $currentTemp = 0;
    protected $engineOn = false;
    protected $horsePower = 5;

    private function cooling() {
        print "Включаем вентилятор охлаждения!<br>";
        $this->currentTemp -= 10;
    }

    protected function onMove($speed)
    {
        $this->currentTemp += ceil($speed / 10) * 5;

        print "Проехали: {$this->currentSpeed}м., текущая дистанция: {$this->distance}, Температура двигателя: {$this->currentTemp}<br>";

        if ($this->currentTemp >= 90) {
            $this->cooling();
        }
    }
}

trait BackwardDirection
{
    protected $allowBack = true;
}

abstract class Transmission
{
    public $curGear = 0; // available -1, 0, 1, 2


    abstract public function selectGear($gear);
}

class TransmissionAuto extends Transmission
{
    use BackwardDirection;

    // gears -1, 0, 1
    public function selectGear($gear)
    {
        if ($gear == -1 && !$this->allowBack) throw new Exception('Ваша машина не позволяет ехать назад!');
        $gear = ($gear >= -1 && $gear <= 1) ? $gear : 0;
        $this->curGear = $gear;
        print "Выбрана передача: {$this->curGear}<br>";
    }
}

class TransmissionManual extends Transmission
{
    use BackwardDirection;
    // gears -1, 0, 1, 2
    public function selectGear($gear)
    {
        if ($gear == -1 && !$this->allowBack) throw new Exception('Ваша машина не позволяет ехать назад!');
        $gear = ($gear >= -1 && $gear <= 2) ? $gear : 0;
        $this->curGear = $gear;
        print "Выбрана передача: {$this->curGear}<br>";
    }
}

class Car
{
    use EngineTrait;

    protected $currentSpeed = 0;
    protected $distance = 0;
    protected $gearbox;

    public function __construct($gearboxManual = true)
    {
        if ($gearboxManual) {
            $this->gearbox = new TransmissionManual();
        } else {
            $this->gearbox = new TransmissionAuto();
        }
    }

    public function turnEngineOn()
    {
        $this->engineOn = true;
        print "Двигатель запущен!<br>";
    }

    public function selectGear($gear)
    {
        $this->gearbox->selectGear($gear);
    }

    public function setSpeed($speed)
    {
        $this->currentSpeed = $speed;
        print "Установлена скорость: {$this->currentSpeed}<br>";
    }

    public function go($dist)
    {
        if (!$this->engineOn || $this->gearbox->curGear == 0 || $this->currentSpeed <= 0) return; // Передача не выбрана или скорость не указана, никуда не едем

        print "Текущая дистанция: {$this->distance}, необходимая дистанция: {$dist}, Температура двигателя: {$this->currentTemp}, Поехали!<br>";

        if ($this->gearbox->curGear > 0) {
            print "Направление вперед!<br>";
            $neededDist = $this->distance + $dist;

            while($this->distance < $neededDist) {
                $this->distance += $this->currentSpeed;
                $this->onMove($this->currentSpeed);
            }
        } else {
            print "Направление назад!<br>";
            $neededDist = $this->distance - $dist;
            while($this->distance > $neededDist) {
                $this->distance -= $this->currentSpeed;
                $this->onMove($this->currentSpeed);
            }
        }
        print "Мы приехали!<br>";

    }
}

class Niva extends Car
{
    public function __construct($gearboxManual = true)
    {
        parent::__construct($gearboxManual);
    }
}


$niva = new Niva();
$niva->turnEngineOn();
$niva->selectGear(1);
$niva->setSpeed(10); // 10 м/с = 36 км/ч (мс * 3,6 = кмч. кмч/3,6 = мс)
$niva->go(200);
$niva->selectGear(-1);
$niva->setSpeed(5);
$niva->go(180);
