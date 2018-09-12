<?php

class spCopyModelsFilesTask extends sfBaseTask
{
    protected function configure()
    {
        // // add your own arguments here
        // $this->addArguments(array(
        //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
        // ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
            // add your own options here
        ));

        $this->namespace = 'sp';
        $this->name = 'spCopyModelsFiles';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [spCopyModelsFiles|INFO] task does things.
Call it with:

  [php symfony spCopyModelsFiles|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array())
    {
        set_time_limit(0);
        ini_set('memory_limit', '4000M');

        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        /*$modelsList = AgreementModelTable::getInstance()->createQuery()
            ->select('id, dealer_id, model_file, model_record_file, model_file1, model_file2, model_file3, model_file4, model_file5, model_file6, model_file7, model_file8, model_file9, model_file10,
                        model_record_file1, model_record_file2, model_record_file3, model_record_file4, model_record_file5, model_record_file6, model_record_file7, model_record_file8, model_record_file9, model_record_file10')
            ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

        foreach ($modelsList as $model_item) {
            $model_type = 'model';
            if (!empty($model_item['model_record_file'])) {
                $model_type = 'model_scenario';
                $model_record_type = 'model_record';

                $files_item = new AgreementModelReportFiles();
                $files_item->setArray(
                    array(
                        'file' => $model_item['model_record_file'],
                        'object_id' => $model_item['id'],
                        'object_type' => 'model',
                        'file_type' => $model_record_type,
                        'field' => '',
                        'field_name' => ''
                    )
                );
                $files_item->save();

                for($file_ind = 1; $file_ind <= 10; $file_ind++) {
                    $file_name = $model_item['model_record_file'.$file_ind];
                    if (!empty($file_name)) {
                        $files_item = new AgreementModelReportFiles();
                        $files_item->setArray(
                            array(
                                'file' => $model_item['model_record_file'.$file_ind],
                                'object_id' => $model_item['id'],
                                'object_type' => 'model',
                                'file_type' => $model_record_type,
                                'field' => '',
                                'field_name' => ''
                            )
                        );
                        $files_item->save();
                    }
                }
            }

            $files_item = new AgreementModelReportFiles();
            $files_item->setArray(
                array(
                    'file' => $model_item['model_file'],
                    'object_id' => $model_item['id'],
                    'object_type' => 'model',
                    'file_type' => $model_type,
                    'field' => '',
                    'field_name' => ''
                )
            );
            $files_item->save();

            for($file_ind = 1; $file_ind <= 10; $file_ind++) {
                $file_name = $model_item['model_file'.$file_ind];
                if (!empty($file_name)) {
                    $files_item = new AgreementModelReportFiles();
                    $files_item->setArray(
                        array(
                            'file' => $model_item['model_file'.$file_ind],
                            'object_id' => $model_item['id'],
                            'object_type' => 'model',
                            'file_type' => $model_type,
                            'field' => '',
                            'field_name' => ''
                        )
                    );
                    $files_item->save();
                }
            }

        }*/

        /*$reports_files = AgreementModelReportTable::getInstance()->createQuery()->select('id, financial_docs_file, additional_file,
            additional_file2, additional_file3, additional_file4, additional_file5, additional_file6, additional_file7,
            financial_docs_file1, financial_docs_file2, financial_docs_file3, financial_docs_file4, financial_docs_file5, financial_docs_file6, financial_docs_file7, financial_docs_file8, financial_docs_file9, financial_docs_file10,
            additional_file_ext1, additional_file_ext2, additional_file_ext3, additional_file_ext4, additional_file_ext5, additional_file_ext6, additional_file_ext7, additional_file_ext8, additional_file_ext9, additional_file_ext10')
            ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

        foreach ($reports_files as $report_item) {
            if (!empty($report_item['financial_docs_file'])) {
                $files_item = new AgreementModelReportFiles();
                $files_item->setArray(
                    array(
                        'file' => $report_item['financial_docs_file'],
                        'object_id' => $report_item['id'],
                        'object_type' => 'report',
                        'file_type' => 'report_financial',
                        'field' => '',
                        'field_name' => ''
                    )
                );
                $files_item->save();
            }

            if (!empty($report_item['additional_file'])) {
                $files_item = new AgreementModelReportFiles();
                $files_item->setArray(
                    array(
                        'file' => $report_item['additional_file'],
                        'object_id' => $report_item['id'],
                        'object_type' => 'report',
                        'file_type' => 'report_additional',
                        'field' => '',
                        'field_name' => ''
                    )
                );
                $files_item->save();
            }

            for ($file_ind = 2; $file_ind <= 7; $file_ind++) {
                if (!empty($report_item['additional_file'.$file_ind]) && !is_null($report_item['additional_file'.$file_ind])) {
                    $files_item = new AgreementModelReportFiles();
                    $files_item->setArray(
                        array(
                            'file' => $report_item['additional_file' . $file_ind],
                            'object_id' => $report_item['id'],
                            'object_type' => 'report',
                            'file_type' => 'report_additional',
                            'field' => '',
                            'field_name' => ''
                        )
                    );
                    $files_item->save();
                }
            }

            for ($file_ind = 1; $file_ind <= 10; $file_ind++) {
                if (!empty($report_item['additional_file_ext'.$file_ind]) && !is_null($report_item['additional_file_ext'.$file_ind])) {
                    $files_item = new AgreementModelReportFiles();
                    $files_item->setArray(
                        array(
                            'file' => $report_item['additional_file_ext' . $file_ind],
                            'object_id' => $report_item['id'],
                            'object_type' => 'report',
                            'file_type' => 'report_additional',
                            'field' => '',
                            'field_name' => ''
                        )
                    );
                    $files_item->save();
                }
            }

            for ($file_ind = 1; $file_ind <= 10; $file_ind++) {
                if (!empty($report_item['financial_docs_file'.$file_ind]) && !is_null($report_item['financial_docs_file'.$file_ind])) {
                    $files_item = new AgreementModelReportFiles();
                    $files_item->setArray(
                        array(
                            'file' => $report_item['financial_docs_file' . $file_ind],
                            'object_id' => $report_item['id'],
                            'object_type' => 'report',
                            'file_type' => 'report_financial',
                            'field' => '',
                            'field_name' => ''
                        )
                    );
                    $files_item->save();
                }
            }
        }*/

        $materials = MaterialTable::getInstance()->createQuery()->select('id, activity_id')->orderBy('id ASC')->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
        foreach ($materials as $material) {
            $item = new ActivityMaterials();

            $item->setArray(
                array(
                    'activity_id' => $material['activity_id'],
                    'material_id' => $material['id']
                )
            );
            $item->save();
        }

        // add your code here
    }
}
