<?php

/**
 * material actions.
 *
 * @package    Servicepool2.0
 * @subpackage material
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class materialActions extends sfActions
{

    /**
     * Executes index action
     *
     * @param sfRequest $request A request object
     */
    function executeIndex(sfWebRequest $request)
    {
        $this->activity = ActivityTable::getInstance()->find($request->getParameter('activity'));
        $this->forward404Unless($this->activity);


        $builder = new MaterialsListBuilder($this->activity);
        $builder->build($this->getUser()->getAuthUser());
        $this->activities = $builder;
    }

    function executeMaterial(sfWebRequest $request)
    {
        $material = MaterialTable::getInstance()->find($request->getParameter('id'));
        $this->forward404Unless($material);

        $material->markAsViewed($this->getUser()->getAuthUser());

        $data = array(
            'name' => $material->getName(),
            'web_previews' => array(),
            'sources' => array()
        );

        if ($material->getFilePreview()) {
            $preview_file_helper = $material->getPreviewFileNameHelper();
            $data['file_preview'] = array(
                'file' => $material->getFilePreview(),
                'size' => $preview_file_helper->getSize(),
                'smart_size' => $preview_file_helper->getSmartSize(),
                'ext' => $preview_file_helper->getKnownExtensionIf()
            );
        } else {
            $data['file_preview'] = false;
        }

        foreach ($material->getWebPreviews() as $web_preview)
            $data['web_previews'][] = $web_preview->getFile();

        foreach ($material->getSources() as $source) {
            $file_name_helper = $source->getFileNameHelper();
            $data['sources'][] = array(
                'id' => $source->getId(),
                'name' => $source->getName(),
                'file' => $source->getFile(),
                'size' => $file_name_helper->getSize(),
                'smart_size' => $file_name_helper->getSmartSize(),
                'ext' => $file_name_helper->getKnownExtensionIf(),
                'known_ext' => $file_name_helper->getKnownExtension()
            );
        }

        if ($material->getEditorLink())
            $data['editor_link'] = $material->getEditorLink();

        $this->getResponse()->setContentType('application/json');
        $this->getResponse()->setContent(json_encode($data));

        return sfView::NONE;
    }

    public function executeDownload(sfWebRequest $request)
    {
        $id = $request->getParameter('id');

        $source = MaterialSourceTable::getInstance()->find($id);
        if ($source) {
            /*$count = $source->getDownloads();
            $source->setDownloads(++$count);
            $source->save();*/

            $item = new MaterialDownloads();
            $item->setMaterialId($id);
            $item->setUserId($this->getUser()->getAuthUser()->getId());
            $item->save();

            $filePath = sfConfig::get('app_materials_upload_path') . '/source/' . $source->getFile();
            $file_download_result = F::downloadFile($filePath, $source->getFile());

            if (empty($file_download_result)) {
                $this->getResponse()->setContentType('application/json');
                $this->getResponse()->setContent(json_encode(array('success' => false, 'message' => 'Файл не найден')));
            } else {
                $file_download_result != 'success' ? $this->redirect($file_download_result) : '';
            }

            //$this->redirect('/uploads/materials/source/'.$source->getFile());
        } else {
            $this->getResponse()->setContentType('application/json');
            $this->getResponse()->setContent(json_encode(array('success' => false, 'message' => 'Файл не найден')));
        }

        return sfView::NONE;
    }
}
