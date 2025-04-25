<?php
require_once ("Model.php");

class InternalEvent extends Model {
    public string $link;
    public bool $isPublic;
    public bool $isCancelled;
    public string $publishDateTime;
    public string $eventDateTime;
    public string $shortDescription;
    public string $contentHTML;
    public string $metaDescription;
    public string $metaTags;
}

