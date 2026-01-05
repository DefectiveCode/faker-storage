<?php

declare(strict_types=1);

namespace DefectiveCode\Faker\Generators;

use Random\Randomizer;
use Random\Engine\Mt19937;
use Symfony\Component\Mime\Address;
use DefectiveCode\Faker\NameGenerator;
use DefectiveCode\Faker\Configs\Config;
use DefectiveCode\Faker\Concerns\SetsSeed;
use DefectiveCode\Faker\Configs\EmailConfig;
use DefectiveCode\Faker\Concerns\PreparesFaker;
use Symfony\Component\Mime\Email as SymfonyEmail;

class Email implements Generator
{
    use PreparesFaker;
    use SetsSeed;

    /**
     * @param  EmailConfig  $config
     */
    public function __construct(public Config $config) {}

    public static function getDefaultConfig(): Config
    {
        return new EmailConfig([
            'contentType' => 'message/rfc822',
            'nameGenerator' => NameGenerator::default('eml'),
        ]);
    }

    public function generate(): mixed
    {
        $email = (new SymfonyEmail)
            ->to(new Address($this->faker->email(), $this->faker->name()))
            ->from(new Address($this->faker->email(), $this->faker->name()))
            ->subject($this->faker->sentence())
            ->text($this->getBody());

        $email = $this->attachAttachments($email)->toString();

        $email = preg_replace_callback('/Message-ID: <([0-9a-z]{32})/', function ($matches) {
            $seed = mt_rand();
            $randomizer = new Randomizer(new Mt19937($seed));
            $bytes = $randomizer->getBytes(16);
            $hexString = bin2hex($bytes);

            return str_replace($matches[1], $hexString, $matches[0]);
        }, $email);

        $stream = fopen('php://temp', 'w+');
        fwrite($stream, $email);

        return $stream;
    }

    public function paragraphs(int $minParagraphs, int $maxParagraphs): self
    {
        $this->config->minParagraphs = $minParagraphs;
        $this->config->maxParagraphs = $maxParagraphs;

        return $this;
    }

    public function sentences(int $minSentences, int $maxSentences): self
    {
        $this->config->minSentences = $minSentences;
        $this->config->maxSentences = $maxSentences;

        return $this;
    }

    public function withAttachment(Generator|string $attachmentGenerator = Png::class, int $minAttachments = 1, int $maxAttachments = 3): self
    {
        $this->config->attachmentGenerator = $attachmentGenerator;
        $this->config->minAttachments = $minAttachments;
        $this->config->maxAttachments = $maxAttachments;

        return $this;
    }

    protected function getBody(): string
    {
        $bodyParagraphs = [];
        $paragraphCount = mt_rand($this->config->minParagraphs, $this->config->maxParagraphs);

        for ($i = 0; $i < $paragraphCount; $i++) {
            $bodyParagraphs[] = $this->faker->paragraph(mt_rand($this->config->minSentences, $this->config->maxSentences));
        }

        return implode("\n\n", $bodyParagraphs);
    }

    protected function attachAttachments(SymfonyEmail $email): SymfonyEmail
    {
        if (! isset($this->config->attachmentGenerator)) {
            return $email;
        }

        $attachmentCount = mt_rand($this->config->minAttachments, $this->config->maxAttachments);

        for ($i = 0; $i < $attachmentCount; $i++) {
            $attachmentStream = $this->generateAttachment();
            $email->attach($attachmentStream, $this->faker->word().'.dat', $this->getAttachmentContentType());
        }

        return $email;
    }

    protected function generateAttachment()
    {
        $generator = is_string($this->config->attachmentGenerator)
            ? new ($this->config->attachmentGenerator)(($this->config->attachmentGenerator)::getDefaultConfig())
            : $this->config->attachmentGenerator;

        if (method_exists($generator, 'prepare')) {
            $generator->prepare();
        }

        $generator->setSeed(mt_rand());

        return $generator->generate();
    }

    protected function getAttachmentContentType(): string
    {
        $generator = is_string($this->config->attachmentGenerator)
            ? new ($this->config->attachmentGenerator)(($this->config->attachmentGenerator)::getDefaultConfig())
            : $this->config->attachmentGenerator;

        return $generator->config->contentType;
    }
}
