<?php
require_once PROJECT_ROOT . DIRECTORY_SEPARATOR . 'autoloader.php';

class ContentHelper
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
        if (is_null($document_id)) {
            return DbHelper::shared()->count('fnf_contents');
        } else {
            return DbHelper::shared()->count('fnf_contents', array('[><]fnf_documents' => array('document_id' => 'document_id')), array('fnf_contents.content_id'), array('AND' => array('fnf_contents.document_id' => $document_id)));
        }
    }

    public function getContents($page_index, $page_size, $document_id = NULL)
    {
        $contents = array();
        if (is_null($document_id)) {
            $contents = DbHelper::shared()->select('fnf_contents', array('fnf_contents.content_id(id)', 'fnf_contents.document_id(document_id)', 'fnf_contents.content_name(name)', 'fnf_contents.content_description(description)', 'fnf_contents.content_type(type)', 'fnf_contents.scale_x(sx)', 'fnf_contents.scale_y(sy)', 'fnf_contents.origin_x(x)', 'fnf_contents.origin_y(y)', 'fnf_contents.size_width(w)', 'fnf_contents.size_height(h)', 'fnf_contents.page_number(page_number)'), array('LIMIT' => [$page_index, $page_size]));
        } else {
            $contents = DbHelper::shared()->select('fnf_contents', array('[><]fnf_documents' => array('document_id' => 'document_id')), array('fnf_contents.content_id(id)', 'fnf_contents.document_id(document_id)', 'fnf_contents.content_name(name)', 'fnf_contents.content_description(description)', 'fnf_contents.content_type(type)', 'fnf_contents.scale_x(sx)', 'fnf_contents.scale_y(sy)', 'fnf_contents.origin_x(x)', 'fnf_contents.origin_y(y)', 'fnf_contents.size_width(w)', 'fnf_contents.size_height(h)', 'fnf_contents.page_number(page_number)'), array('AND' => array('fnf_documents.document_id' => $document_id), 'ORDER' => array('fnf_contents.content_id' => 'ASC'), 'LIMIT' => [$page_index, $page_size]));
        }
        
        return $contents;
    }

    public function getContentsByDocumentId($document_id)
    {
        $contents = array();
        if (is_null($document_id) == false) {
            $contents = DbHelper::shared()->select('fnf_contents', array('[><]fnf_documents' => array('document_id' => 'document_id')), array('fnf_contents.content_id(id)', 'fnf_contents.document_id(document_id)', 'fnf_contents.content_name(name)', 'fnf_contents.content_description(description)', 'fnf_contents.content_type(type)', 'fnf_contents.scale_x(sx)', 'fnf_contents.scale_y(sy)', 'fnf_contents.origin_x(x)', 'fnf_contents.origin_y(y)', 'fnf_contents.size_width(w)', 'fnf_contents.size_height(h)', 'fnf_contents.page_number(page_number)'), array('AND' => array('fnf_documents.document_id' => $document_id), 'ORDER' => array('fnf_contents.content_id' => 'ASC')));
        }
        
        return $contents;
    }

    public function getContentById($id)
    {
		$contents = array();
        $contents = DbHelper::shared()->select('fnf_contents', array('fnf_contents.content_id(id)', 'fnf_contents.document_id(document_id)', 'fnf_contents.content_name(name)', 'fnf_contents.content_description(description)', 'fnf_contents.content_type(type)', 'fnf_contents.scale_x(sx)', 'fnf_contents.scale_y(sy)', 'fnf_contents.origin_x(x)', 'fnf_contents.origin_y(y)', 'fnf_contents.size_width(w)', 'fnf_contents.size_height(h)', 'fnf_contents.page_number(page_number)'), array('AND' => array('fnf_contents.content_id' => $id)));
		if ((sizeof($contents) > 0) && (sizeof($contents[0]) > 0)) {
			return $contents[0];
		} else {
			return NULL;
		}
    }

    public function deleteContentsByDocumentId($document_id)
    {
        DbHelper::shared()->delete('fnf_contents', array('AND' => array('document_id' => $document_id)));
    }

    public function deleteContent($id)
    {
        DbHelper::shared()->delete('fnf_contents', array('AND' => array('content_id' => $id)));
    }

    public function deleteContentsNotIn($id, $document_id)
    {
        DbHelper::shared()->delete('fnf_contents', array('AND' => array('content_id[!]' => $id, 'document_id' => $document_id)));
    }

    public function createContent($document_id, $name, $description, $type, $scale_x, $scale_y, $origin_x, $origin_y, $size_width, $size_height, $page_number)
    {
        DbHelper::shared()->insert('fnf_contents', array('document_id' => $document_id, 'content_name' => $name, 'content_description' => $description, 'content_type' => $type, 'scale_x' => $scale_x, 'scale_y' => $scale_y, 'origin_x' => $origin_x, 'origin_y' => $origin_y, 'size_width' => $size_width, 'size_height' => $size_height, 'page_number' => $page_number));
		$id = DbHelper::shared()->id();

        return $id;
    }

    public function updateContent($id, $document_id, $name, $description, $type, $scale_x, $scale_y, $origin_x, $origin_y, $size_width, $size_height, $page_number)
    {
        DbHelper::shared()->update('fnf_contents', array('content_name' => $name, 'content_description' => $description, 'content_type' => $type, 'scale_x' => $scale_x, 'scale_y' => $scale_y, 'origin_x' => $origin_x, 'origin_y' => $origin_y, 'size_width' => $size_width, 'size_height' => $size_height, 'page_number' => $page_number), array('AND' => array('content_id' => $id, 'document_id' => $document_id)));
    }
}
?>
