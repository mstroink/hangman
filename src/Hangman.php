<?php
declare(strict_types=1);
namespace mstroink\Hangman;

class Hangman
{
    const MAX_ATTEMPTS = 7;

    private $guessedLetters = [];
    private $maskedWord;
    private $secretWord;
    private $attempts;

    function __construct()
    {
        $this->newGame();
    }

    public function newGame(?string $word = null): void
    {
        if (is_null($word)) {
            $word = (new WordList())->getRandomWord();
        }

        $this->secretWord = $word;
        $this->maskSecretWord();
        $this->hint();
        $this->attempts = 0;
    }

    private function maskSecretWord(): void
    {
        $this->maskedWord = str_repeat("_", strlen($this->secretWord));
    }

    private function hint(): void
    {
        $letters = str_split($this->getSecretWord());

        do {
            $key = array_rand($letters);
        } while(preg_match("/(a|e|i)/i", $letters[$key]));

        $this->guessLetter($letters[$key]);
    }

    public function guess(string $letters)
    {
        foreach (str_split($letters) as $letter) {
            $letter = strtoupper($letter);

            if (array_search($letter, $this->guessedLetters) !== false) {
                continue;
            }

            $this->guessLetter($letter);

            if ($this->isOver()) {
                break;
            }
        }
    }

    private function guessLetter(string $letter) : string
    {
        if (strstr($this->secretWord, $letter)) {
            $offset = 0;
            while (($pos = strpos($this->secretWord, $letter, $offset)) !== false) {
                $this->maskedWord = substr_replace($this->maskedWord, $letter, $pos, 1);
                $offset = $pos + 1;
            }
        } else {
            array_push($this->guessedLetters, $letter);
            $this->attempts += 1;
        }

        return $letter;
    }

    /**
     * @return bool
     */
    public function hasWon(): bool
    {
        return $this->maskedWord == $this->secretWord;
    }
    /**
     * @return bool
     */
    public function hasLost(): bool
    {
        return (!$this->hasWon() && ($this->getAttempts() == self::MAX_ATTEMPTS));
    }
    /**
     * @return bool
     */
    public function isOver(): bool
    {
        return ($this->hasWon() || ($this->getAttempts() == self::MAX_ATTEMPTS));
    }

    public function getGuessedLetters(): string
    {
        return empty($this->guessedLetters) ? "none" : implode($this->guessedLetters, ", ");
    }

    public function getAttempts(): int
    {
        return $this->attempts;
    }

    public function getAttemptsLeft(): int
    {
        return self::MAX_ATTEMPTS - $this->attempts;
    }

    public function getMaskedWord(): string
    {
        return $this->maskedWord;
    }

    public function getSecretWord(): string
    {
        return $this->secretWord;
    }
}
