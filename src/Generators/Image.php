<?php

declare(strict_types=1);

namespace DefectiveCode\Faker\Generators;

use GdImage;
use DefectiveCode\Faker\Configs\Config;
use DefectiveCode\Faker\Concerns\SetsSeed;
use DefectiveCode\Faker\Configs\ImageConfig;

abstract class Image implements Generator
{
    use SetsSeed;

    /**
     * @param  ImageConfig  $config
     */
    public function __construct(public Config $config) {}

    abstract protected function imageCreator(): string;

    public function grid(int $size): self
    {
        $this->config->gridSize = $size;

        return $this;
    }

    public function height(int $minHeight, int $maxHeight): self
    {
        $this->config->minHeight = $minHeight;
        $this->config->maxHeight = $maxHeight;

        return $this;
    }

    public function width(int $minWidth, int $maxWidth): self
    {
        $this->config->minWidth = $minWidth;
        $this->config->maxWidth = $maxWidth;

        return $this;
    }

    public function generate(): mixed
    {
        $image = imagecreatetruecolor(
            $width = mt_rand($this->config->minWidth, $this->config->maxWidth),
            $height = mt_rand($this->config->minHeight, $this->config->maxHeight)
        );

        $this->config->gridSize
            ? $this->generateGridImage($image, $width, $height)
            : $this->generateRandomImage($image, $width, $height);

        $stream = fopen('php://temp', 'w+');
        call_user_func($this->imageCreator(), $image, $stream);
        imagedestroy($image);

        return $stream;
    }

    protected function generateRandomImage(GdImage $image, int $width, int $height): void
    {
        if ($hasAlpha = $this->config->withAlpha ?? false) {
            imagealphablending($image, false);
            imagesavealpha($image, true);
        }

        for ($row = 0; $row < $width; $row++) {
            for ($column = 0; $column < $height; $column++) {
                $color = imagecolorallocatealpha(
                    $image,
                    mt_rand(0, 255),
                    mt_rand(0, 255),
                    mt_rand(0, 255),
                    mt_rand(0, $hasAlpha ? 127 : 0)
                );

                imagesetpixel($image, $row, $column, $color);
            }
        }
    }

    protected function generateGridImage(GdImage $image, int $width, int $height): void
    {
        $paddingPercent = .1;
        $gridSize = $this->config->gridSize;
        $usableWidth = $width * (1 - $paddingPercent * 2);
        $usableHeight = $height * (1 - $paddingPercent * 2);

        // Calculate block size to fit within the random dimensions
        // Use the smaller dimension to ensure it fits, with some padding
        $blockSize = (int) floor(min($usableWidth, $usableHeight) / $gridSize);

        // Center the grid in the image
        $gridWidth = $gridSize * $blockSize;
        $gridHeight = $gridSize * $blockSize;
        $offsetX = (int) (($width - $gridWidth) / 2);
        $offsetY = (int) (($height - $gridHeight) / 2);

        imagefill(
            $image,
            0,
            0,
            imagecolorallocate($image, 240, 240, 240)
        );

        $foregroundColor = imagecolorallocate(
            $image,
            mt_rand(0, 255),
            mt_rand(0, 255),
            mt_rand(0, 255),
        );

        // Generate symmetric pattern
        // Only need to generate half (rounded up) since it's mirrored
        $halfGrid = (int) ceil($gridSize / 2);

        for ($row = 0; $row < $gridSize; $row++) {
            for ($column = 0; $column < $halfGrid; $column++) {
                // Random decision: fill or leave empty
                if (mt_rand(0, 1) === 1) {
                    // Draw on the left side
                    $x = $offsetX + ($column * $blockSize);
                    $y = $offsetY + ($row * $blockSize);

                    imagefilledrectangle(
                        $image,
                        $x,
                        $y,
                        $x + $blockSize - 1,
                        $y + $blockSize - 1,
                        $foregroundColor
                    );

                    // Mirror to the right side (unless it's the center column on odd grids)
                    $mirrorCol = $gridSize - 1 - $column;
                    if ($mirrorCol !== $column) {
                        $mirrorX = $offsetX + ($mirrorCol * $blockSize);

                        imagefilledrectangle(
                            $image,
                            $mirrorX,
                            $y,
                            $mirrorX + $blockSize - 1,
                            $y + $blockSize - 1,
                            $foregroundColor
                        );
                    }
                }
            }
        }
    }
}
