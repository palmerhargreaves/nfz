<?php

/**
 * temp_file actions.
 *
 * @package    Servicepool2.0
 * @subpackage temp_file
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class temp_fileActions extends ActionsWithJsonForm
{
    /**
     * Executes index action
     *
     * @param sfRequest $request A request object
     */
    public function executeUpload(sfWebRequest $request)
    {
        $form = new TempFileForm();
        $form->bind(array(
            'user_id' => $this->getUser()->getAuthUser()->getId()
        ), $request->getFiles());

        if ($form->isValid()) {
            $form->save();
            $this->file = $form->getObject();
            $this->success = true;
        } else {
            $this->success = false;
        }
    }

    public function executeDelete(sfWebRequest $request)
    {
        $file = TempFileTable::getInstance()
            ->createQuery()
            ->where('user_id=? and id=?', array($this->getUser()->getAuthUser()->getId(), $request->getParameter('id')))
            ->fetchOne();

        if ($file)
            $file->delete();

        return sfView::NONE;
    }

    public function executeUploadAjax(sfWebRequest $request)
    {
        $file_object_type = $request->getParameter('upload_file_object_type');
        $file_type = $request->getParameter('upload_file_type');
        $upload_field = $request->getParameter('upload_field');

        $uploaded_files = Utils::getUploadedFilesList($request, $upload_field);

        $tmp_files = array();
        if (!empty($uploaded_files) && isset($uploaded_files[$upload_field])) {
            $tmp_files['file'] = $uploaded_files[$upload_field];
        }

        $form = new TempFileForm($file_object_type, $file_type);
        $form->bind(array(
            'user_id' => $this->getUser()->getAuthUser()->getId(),
            'activity_id' => sfContext::getInstance()->getUser()->getAttribute('last_activity_id'),
            'file_object_type' => $file_object_type,
            'file_type' => $file_type
        ), $tmp_files);

        if ($form->isValid()) {
            $form->save();

            return $this->sendJson
            (
                array
                (
                    'success' => true,
                    'file' => array
                    (
                        'id' => $form->getObject()->getId(),
                        'name' => $form->getObject()->getFile(),
                        'size' => $tmp_files['file']['size'],
                        'path' => F::isImage($form->getObject()->getFile()) ? '/uploads/'.TempFile::FILE_PATH.'/'.$form->getObject()->getFile() : ''
                    )
                )
            );
        } else {
            foreach ($form->getErrorSchema()->getErrors() as $name => $error) {
                $errors[] = array('name' => $name, 'message' => self::getErrors($error) . '.');
            }

            return $this->sendJson(array('success' => false, 'errors' => $errors, 'file_name' => isset($tmp_files['file']) ? $tmp_files['file']['name'] : null));
        }

        return $this->sendJson(array('success' => false));
    }

    public function executeUploadAjaxDelete(sfWebRequest $request)
    {
        $temp = TempFileTable::getInstance()->find($request->getParameter('id'));
        if ($temp) {
            TempFileTable::removeFile($temp);

            return $this->sendJson(array('success' => true));
        }

        return $this->sendJson(array('success' => false));
    }
}
