<?php
declare(strict_types=1);
namespace mstroink\Hangman;

class WordList
{
    const DEFAULT_PATH = 'data/words/nl.txt';

    private $path = null;

    /**
     * WordList constructor.
     * @param string|null $wordListPath
     */
    public function __construct(?string $wordListPath = null)
    {
        $this->path = $wordListPath ?? self::DEFAULT_PATH;
    }

    /**
     * @return string
     */
    public function getRandomWord(): string
    {
        $file = new \SplFileObject($this->path);

        $file->seek($file->getSize());
        $linesTotal = $file->key();

        do {
            $file->seek(rand(0, $linesTotal));
        } while (!preg_match("/^[a-z]{4,12}$/i", $file->current()));

        return (string)strtoupper(trim($file->current()));
    }

}