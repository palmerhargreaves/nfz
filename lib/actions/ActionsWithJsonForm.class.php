<?php

/**
 * Description of ActionsWithJsonForm
 *
 * @author Сергей
 */
class ActionsWithJsonForm extends sfActions
{
    protected function sendFormBindResult(sfForm $form, $frame_callback = false, $redirectTo = '')
    {

        $response = array('success' => true);
        if (!$form->isValid()) {
            $errors = array();

            foreach ($form->getErrorSchema()->getErrors() as $name => $error)
                $errors[] = array('name' => $name, 'message' => self::getErrors($error) . '.');

            $response = array(
                'success' => false,
                'errors' => $errors
            );

            $globalError = $form->renderGlobalErrors();
            if ($globalError && empty($errors))
                $response = array('success' => false,
                    'errors' => $globalError);
        }

        if (!empty($redirectTo))
            $response['redirect'] = $redirectTo;

        return $this->sendJson($response, $frame_callback);
    }

    protected function sendJson($content, $frame_callback = false)
    {
        $json = json_encode($content);

        if ($frame_callback) {
            $this->setLayout(false);

            $this->getResponse()->setContent("<script type='text/javascript'>parent.$frame_callback($json)</script>");
        } else {
            $this->getResponse()->setContentType('application/json');
            $this->getResponse()->setContent($json);
        }

        return sfView::NONE;
    }

    protected function sendError($error)
    {
        return $this->sendJson(array('success' => false, 'error' => $error));
    }

    protected function sendNotFound()
    {
        return $this->sendError('not_found');
    }

    static function getErrors(sfValidatorError $error)
    {
        $errors = array();

        if ($error instanceof sfValidatorErrorSchema) {
            foreach ($error->getErrors() as $err)
                $errors[] = self::getErrors($err);
        } else {
            $errors[] = $error->getMessage();
        }

        return implode('. ', $errors);
    }

    function getCommentFiles(sfWebRequest $request)
    {
        $files = $request->getFiles();
        if (!is_array($files)) {
            return $files;
        }

        $uploaded_files = Utils::getUploadedFilesByField($files, 'agreement_comments_file');
        if (!empty($uploaded_files)) {
            return $uploaded_files;
        }

        $server_file = $request->getPostParameter('server_agreement_comments_file');
        if (!$server_file || preg_match('#[\\\/]#', $server_file)) {
            if (isset($files['agreement_comments_file']) && isset($files['agreement_comments_file'][0])) {
                return array('agreement_comments_file' => $files['agreement_comments_file'][0]);
            }

            return $files;
        }

        $tmp_name = $this->getUser()->getAuthUser()->getDealerUploadPath() . '/' . $server_file;
        if (!file_exists($tmp_name)) {
            return $files;
        }

        $files['agreement_comments_file'] = array(
            'name' => $server_file,
            'tmp_name' => $tmp_name,
            'type' => F::getFileMimeType($server_file)
        );

        return $files;
    }
}
