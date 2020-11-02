<?php
/**
 * Created by Catur Pamungkas
 * Date: 01/11/2020.
 */
if (!empty($_POST['player_num']) && !empty($_POST['dice_num'])) {
    class Dadu
    {
        /** @var int */
        private $topSideVal;

        /**
         * @return int
         */
        public function getTopSideVal()
        {
            return $this->topSideVal;
        }

        /**
         * @return int
         */
        public function roll()
        {
            $this->topSideVal = rand(1, 6);

            return $this;
        }

        /**
         * @param int $topSideVal
         *
         * @return Dadu
         */
        public function setTopSideVal($topSideVal)
        {
            $this->topSideVal = $topSideVal;

            return $this;
        }
    }

    class Pemain
    {
        /** @var array */
        private $diceInCup = [];
        /** @var string */
        private $name;
        /** @var int */
        private $position;

        /**
         * Pemain constructor.
         *
         * @param int   $numberOfDice
         * @param mixed $position
         * @param mixed $name
         */
        public function __construct($numberOfDice, $position, $name = '')
        {
            //position 0 is the left most
            $this->position = $position;
            $this->name = $name;
            //init array of dice
            for ($i = 0; $i < $numberOfDice; ++$i) {
                array_push($this->diceInCup, new Dadu());
            }
        }

        /**
         * @return array
         */
        public function getDiceInCup()
        {
            return $this->diceInCup;
        }

        /**
         * @return string
         */
        public function getName()
        {
            return $this->name;
        }

        /**
         * @return int
         */
        public function getPosition()
        {
            return $this->position;
        }

        public function play()
        {
            foreach ($this->diceInCup as $dice) {
                // @var Dice $dice
                $dice->roll();
            }
        }

        /**
         * @param int $key
         */
        public function removeDice($key)
        {
            unset($this->diceInCup[$key]);
        }

        /**
         * @param Dadu $dice
         */
        public function insertDice($dice)
        {
            array_push($this->diceInCup, $dice);
        }
    }

    /**
     * The game is where the rules are applied
     * Class Game.
     */
    class Game
    {
        const RULE_REMOVED_WHEN_DICE_TOP = 6;
        const RULE_MOVE_WHEN_DICE_TOP = 1;

        /** @var Pemain[] */
        private $players = [];
        private $round;

        /**
         * Game constructor.
         */
        public function __construct()
        {
            //init round to 0
            $this->round = 0;
            //the game contains players and each
            //player have dices
            for ($i = 0; $i < $_POST['player_num']; ++$i) {
                $this->players[$i] = new Pemain($_POST['dice_num'], $i, $i + 1);
            }
            $this->points = [];
            $this->totalpoint = [];
        }

        /**
         * @return $this
         */
        public function displayRound()
        {
            $colspan = $_POST['player_num'] + 1;
            echo '<tr><td class="text-center" colspan='.$colspan.'>Putaran '.$this->round.'</td></tr>';

            return $this;
        }

        /**
         * @param string $title
         * @param mixed  $this
         * @param mixed  $round
         *
         * @return $this
         */
        public function displayTopSideDice()
        {
            echo '<tr><td class="text-center">Lempar Dadu</td>';
            foreach ($this->players as $player) {
                /** @var Pemain $player */
                $diceTopSide = '';
                $pointPerRound = 0;
                foreach ($player->getDiceInCup() as $dice) {
                    // @var Dice $dice
                    $diceTopSide .= '<img src="images/Dadu'.$dice->getTopSideVal().'.png" width="25px" height="25px"/> ';
                }

                echo '<td class="text-center">'.$diceTopSide.'</td>';
            }
            echo '</tr>';

            return $this;
        }

        /**
         * @param string $title
         *
         * @return $this
         */
        public function displayTopSideDiceAfter($title = 'Setelah Evaluasi')
        {
            echo '<tr><td class="text-center">'.$title.'</td>';
            foreach ($this->players as $player) {
                /** @var Pemain $player */
                $diceTopSide = '';

                foreach ($player->getDiceInCup() as $dice) {
                    // @var Dice $dice
                    $diceTopSide .= '<img src="images/Dadu'.$dice->getTopSideVal().'.png" width="25px" height="25px"/> ';
                }

                echo '<td class="text-center">'.$diceTopSide.'</td>';
            }
            echo '</tr>';

            return $this;
        }

        /**
         * @param Pemain $player
         *
         * @return $this
         */
        public function displayWinner($player)
        {
            $colspan = $_POST['player_num'] + 1;
            echo '<tr><td colspan='.$colspan.'><h1>Pemenang : Pemain #'.$player->getName().'</h1></td></tr>';

            return $this;
        }

        /**
         * Start the game.
         */
        public function start()
        {
            $points = [];
            $winnerCount = 0;
            //loop until found the winner(s)
            while (true) {
                ++$this->round;
                $diceCarryForward = [];

                //simulate the simultaneous player roll the dice
                foreach ($this->players as $player) {
                    // @var Player $player
                    $player->play();
                }
                //display before moved/removed
                $this->displayRound()->displayTopSideDice();

                //check foreach player the top side
                foreach ($this->players as $index => $player) {
                    /** @var Pemain $player */
                    $tempDiceArray = [];

                    foreach ($player->getDiceInCup() as $diceIndex => $dice) {
                        /** @var Dadu $dice */
                        //check for any occurrence of 6
                        if (self::RULE_REMOVED_WHEN_DICE_TOP == $dice->getTopSideVal()) {
                            $player->removeDice($diceIndex);
                        }
                        //check for occurrence of 1
                        if (self::RULE_MOVE_WHEN_DICE_TOP == $dice->getTopSideVal()) {
                            //determine player position
                            //MAX player is right most side,
                            //so move the dice to left most side
                            if (($_POST['player_num'] - 1) == $player->getPosition()) {
                                $this->players[0]->insertDice($dice);
                                $player->removeDice($diceIndex);
                            } else {
                                array_push($tempDiceArray, $dice);
                                $player->removeDice($diceIndex);
                            }
                        }
                    }

                    $diceCarryForward[$index + 1] = $tempDiceArray;

                    if (array_key_exists($index, $diceCarryForward) && count($diceCarryForward[$index]) > 0) {
                        //insert the dice
                        foreach ($diceCarryForward[$index] as $dice) {
                            $player->insertDice($dice);
                        }
                        //reset
                        $diceCarryForward = [];
                    }
                }

                //tampilkan data Setelah Evaluasi
                $this->displayTopSideDiceAfter();

                foreach ($this->players as $player) {
                    if (count($player->getDiceInCup()) <= 0) {
                        // unset($this->players);

                        $this->displayWinner($player);
                        ++$winnerCount;
                    }
                }

                if ($winnerCount > 0) {
                    //exit the loop
                    break;
                }
            }
        }
    }

    $game = new Game();
    $game->start();
}
