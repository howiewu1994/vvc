<?php
namespace VVC\Model\Data;

class Step
{
    protected $num = 1;
    protected $name = 'Step One';
    protected $text = 'First...';

    protected $pictures = [];
    protected $videos = [];

    public function __construct(int $num, string $name)
    {
        $this->setNum($num);
        $this->setName($name);
    }

    public function getNum() : int
    {
        return $this->num;
    }

    public function setNum(int $num)
    {
        $this->num = $num;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getText() : string
    {
        return $this->text;
    }

    public function setText(string $text)
    {
        $this->text = $text;
    }

    public function getPictures() : array
    {
        return $this->pictures;
    }

    public function setPictures(array $pictures)
    {
        $this->pictures = $pictures;
    }

    public function addPictures(array $pictures)
    {
        foreach ($pictures as $picture) {
            $this->addPicture($picture);
        }
    }

    public function addPicture(string $pathToPicture)
    {
        $this->pictures[] = $pathToPicture;
    }

    public function getVideos() : array
    {
        return $this->videos;
    }

    public function setVideos(array $videos)
    {
        $this->videos = $videos;
    }

    public function addVideos(array $videos)
    {
        foreach ($videos as $video) {
            $this->addVideo($video);
        }
    }

    public function addVideo(string $pathToVideo)
    {
        $this->videos[] = $pathToVideo;
    }
}
