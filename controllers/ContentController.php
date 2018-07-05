<?php
require_once PROJECT_ROOT . DIRECTORY_SEPARATOR . 'autoloader.php';

class ContentController
{
    private static $instance;
    public static function shared()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    protected function __construct()
    {
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

    public function getCount($document_id = NULL)
    {
        return ContentHelper::shared()->getCount($document_id);
    }

    public function getContents($page_index, $page_size, $document_id = NULL)
    {
        $page_index = ($page_index * $page_size);
        $contents = ContentHelper::shared()->getContents($page_index, $page_size, $document_id);
        return $contents;
    }

    public function getContentsByDocumentId($document_id)
    {
        $contents = ContentHelper::shared()->getContentsByDocumentId($document_id);
        return $contents;
    }

    public function getContentById($id)
    {
        $content = ContentHelper::shared()->getContentById($id);
        return $content;
    }

    public function deleteContentsByDocumentId($document_id)
    {
        return ContentHelper::shared()->deleteContentsByDocumentId($document_id);
    }

    public function deleteContent($id)
    {
        ContentHelper::shared()->deleteContent($id);
    }

    public function deleteContentsNotIn($id, $document_id)
    {
        ContentHelper::shared()->deleteContentsNotIn($id, $document_id);
    }

    public function createContent($document_id, $name, $description, $type, $scale_x, $scale_y, $origin_x, $origin_y, $size_width, $size_height, $page_number)
    {
        ContentHelper::shared()->createContent($document_id, $name, $description, $type, $scale_x, $scale_y, $origin_x, $origin_y, $size_width, $size_height, $page_number);
    }

    public function updateContent($id, $document_id, $name, $description, $type, $scale_x, $scale_y, $origin_x, $origin_y, $size_width, $size_height, $page_number)
    {
        ContentHelper::shared()->updateContent($id, $document_id, $name, $description, $type, $scale_x, $scale_y, $origin_x, $origin_y, $size_width, $size_height, $page_number);
    }
}
?>
