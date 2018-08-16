<?php
require "vendor/autoload.php";

use League\CLImate\CLImate as Console;
use mstroink\Hangman\Hangman;

/** @var \League\CLImate\CLImate $Console sds */
$console = new Console();
$console->addArt(__DIR__ . '/data/art/');

do {
    runGame();
} while (playAgain());

function runGame()
{
    global $console;
    $game = new Hangman();

    while (!($game->isOver())) {
        drawStage($game, $console);

        $input = $console->input('Guess a letter');
        $input->accept(function($letter) {
            return (!empty($letter));
        });

        $letters = $input->prompt();
        $game->guess($letters);
    }

    drawStage($game, $console);

    if ($game->hasWon()) {
        $console
            ->green()
            ->out("CONGRATULATIONS! You win!");
    } else {
        $console
            ->red()
            ->out("Sorry, the word was: " . $game->getSecretWord());
    }
}

function playAgain()
{
    global $console;
    $input = $console->confirm('Would you like to play again?');

    return $input->confirmed();
}

function drawStage($game, $console)
{
    $console
        ->clear()
        ->draw('title')
        ->br();

    $padding = $console
        ->padding(20)
        ->char(' ');
    $padding
        ->label('Guesses Left')
        ->result($game->getAttemptsLeft() . "/7");
    $padding
        ->label('Missed Guesses')
        ->result($game->getGuessedLetters());

    $console->br();

    foreach (str_split($game->getMaskedWord()) as $letter) {
        $console
            ->backgroundBlack()
            ->white()
            ->inline(sprintf(" %s ", $letter))
            ->inline(" ");
    }

    $console
        ->br()
        ->br()
        ->draw('stage' . $game->getAttempts())
        ->br();
}