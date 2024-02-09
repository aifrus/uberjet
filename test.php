<?php

class FlightCostCalculator
{
    private $sql;
    private $db_name;
    private $results;
    private $length;
    private $pax;
    private $payload;
    private $pax_wgt;
    private $total_wgt;
    private $base_fare;
    private $sub_total;
    private $wgt_fee;
    private $mil_price;
    private $minutes;
    private $time_price;
    private $airport_fees;
    private $excise_tax;
    private $pax_total;
    private $uber_fee;
    private $pilot_pay;

    public function __construct()
    {
        $this->sql = mysqli_connect("127.0.0.1", "aifr", "aifr", "NASR_INDEX");
        $this->extractDbName();
        $this->fetchResults();
        $this->calculateWeights();
        $this->calculateFares();
    }

    private function extractDbName()
    {
        $result = $this->sql->query("SELECT `name` as `db_name` FROM `INDEX` WHERE `preview` = '0' ORDER BY `id` DESC LIMIT 1")->fetch_assoc();
        $this->db_name = $result['db_name'];
        mysqli_select_db($this->sql, $this->db_name);
    }

    private function fetchResults()
    {
        $this->results = $this->sql->query("SELECT * FROM `CDR` WHERE `COORDREQ` = 'N' AND `LENGTH` > 0 ORDER BY RAND() LIMIT 1")->fetch_all(MYSQLI_ASSOC);
        $this->length = (int)$this->results[0]['LENGTH'];
    }

    private function calculateWeights()
    {
        $this->pax = rand(1, 8);
        $this->payload = rand(0, 2000);
        $this->pax_wgt = $this->pax * 170;
        $this->total_wgt = $this->payload + $this->pax_wgt;
    }

    private function calculateFares()
    {
        $this->base_fare = 500;
        $this->sub_total = $this->base_fare;
        $this->wgt_fee = round($this->total_wgt * 0.25, 0);
        $this->sub_total += $this->wgt_fee;
        $this->mil_price = round($this->length * 6.25, 0);
        $this->sub_total += $this->mil_price;
        $this->minutes = round(($this->length / 480) * 60, 0) + 30;
        $this->time_price = round($this->minutes * 3.5, 0);
        $this->sub_total += $this->time_price;
        $this->airport_fees = 2 * 100; // 2 @ $100 ea
        $this->sub_total += $this->airport_fees;
        $this->excise_tax = round($this->sub_total * 0.075, 0);
        $this->pax_total = $this->sub_total + $this->excise_tax;
        $this->uber_fee = round($this->pax_total * 0.1, 0);
        $this->pilot_pay = round($this->pax_total - $this->uber_fee - $this->airport_fees - $this->excise_tax, 0);
    }

    private function dollars($in)
    {
        return '$' . number_format($in, 2, ".", ",");
    }

    private function thousands($in)
    {
        return number_format($in, 0, ".", ",");
    }

    public function displayResults(string $origin)
    {
        // try to find the origin in the database
        $origin = mysqli_real_escape_string($this->sql, $origin);
        $result = $this->sql->query("SELECT * FROM `APT_BASE` WHERE `ARPT_ID` = '$origin'")->fetch_assoc() ?? null;
        if (!$result) {
            echo "Origin airport not found\n";
            exit(1);
        }
        print_r($result);
        print_r($this->results[0]);
        printf(
            "Passengers: %s (%s lbs.), Baggage/Cargo: %s lbs, Total %s lbs.\n\n",
            $this->pax,
            $this->thousands($this->pax_wgt),
            $this->thousands($this->payload),
            $this->thousands($this->total_wgt)
        );

        $format = "%-15s|%-15s|%-15s|%-15s|%-15s\n";
        printf($format, "Item", "Qty.", "Ea.", "Amount", "Total");
        printf($format, "Base Fare", "1", "$500.00", $this->dollars($this->base_fare), $this->dollars($this->base_fare));

        $this->sub_total = $this->base_fare + $this->wgt_fee;
        printf($format, "Weight Fee", $this->thousands($this->total_wgt) . " LBS", "$0.25", $this->dollars($this->wgt_fee), $this->dollars($this->sub_total));

        $this->sub_total += $this->mil_price;
        printf($format, "Distance", $this->thousands($this->length) . " NM", "$6.25", $this->dollars($this->mil_price), $this->dollars($this->sub_total));

        $this->sub_total += $this->time_price;
        printf($format, "Time", $this->minutes . " Min", "$3.50", $this->dollars($this->time_price), $this->dollars($this->sub_total));

        $this->sub_total += $this->airport_fees;
        printf($format, "Airport Fees", "2", "$100.00", $this->dollars($this->airport_fees), $this->dollars($this->sub_total));

        $this->sub_total += $this->excise_tax;
        printf($format, "Excise Tax", $this->dollars($this->sub_total - $this->excise_tax), "7.5%", $this->dollars($this->excise_tax), $this->dollars($this->sub_total));


        printf(
            "\nPassenger Total Cost:\t%s\n\n",
            $this->dollars($this->pax_total)
        );

        printf(
            "Airport Fees\t|%s\t|%s\n",
            $this->dollars($this->airport_fees),
            $this->dollars($this->pax_total - $this->airport_fees)
        );

        printf(
            "Excise Tax\t|%s\t|%s\n",
            $this->dollars($this->excise_tax),
            $this->dollars($this->pax_total - $this->excise_tax - $this->airport_fees)
        );

        printf(
            "Uber Fee\t|%s\t|%s\n",
            $this->dollars($this->uber_fee),
            $this->dollars($this->pilot_pay)
        );

        printf(
            "\nPilot Total Pay:\t%s\n",
            $this->dollars($this->pilot_pay)
        );
    }
}

$origin = $argv[1] ?? null;

if (!$origin) {
    echo "Please provide an origin airport code\n";
    exit(1);
}

$length = strlen($origin);
if ($length < 3 || $length > 4) {
    echo "Please provide a valid 3 or 4 character airport code\n";
    exit(1);
}

$origin = ($length === 4) ? substr($origin, 1) : $origin;
$origin = strtoupper($origin);

$calculator = new FlightCostCalculator();
$calculator->displayResults($origin);
