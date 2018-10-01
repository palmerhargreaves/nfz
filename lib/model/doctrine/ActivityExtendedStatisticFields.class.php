<?php

/**
 * ActivityExtendedStatisticFields
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    Servicepool2.0
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class ActivityExtendedStatisticFields extends BaseActivityExtendedStatisticFields
{
	const FIELD_TYPE_VALUE = 'dig';
	const FIELD_TYPE_ANY_VALUE = 'any';
	const FIELD_TYPE_DATE = 'date';
	const FIELD_TYPE_CALC = 'calc';
	const FIELD_TYPE_TEXT = 'text';
    const FIELD_TYPE_FILE = 'file';
    const FIELD_TYPE_MONEY = 'money';

	const FIELD_CALC_SYMBOL_PLUS = 'plus';
	const FIELD_CALC_SYMBOL_MINUS = 'minus';
	const FIELD_CALC_SYMBOL_DIVIDE = 'divide';
	const FIELD_CALC_SYMBOL_PERCENT = 'percent';
    const FIELD_CALC_SYMBOL_MULTIPLE = 'multiple';

	private $_fieldTypes = array(
	    self::FIELD_TYPE_DATE => 'Дата',
        self::FIELD_TYPE_VALUE => 'Значение',
        self::FIELD_TYPE_CALC => 'Вычисляемое значение',
        self::FIELD_TYPE_TEXT => 'Текст',
        self::FIELD_TYPE_ANY_VALUE => 'Цифры / Символы',
        self::FIELD_TYPE_FILE => 'Файл',
        self::FIELD_TYPE_MONEY => 'Копейки');

	public function getFieldType() {
		return $this->_fieldTypes[$this->getValueType()];
	} 

	public function getFieldValue() {
		if($this->getValueType() == self::FIELD_TYPE_CALC) {
			$fieldsResult = array();
			$calFields = ActivityExtendedStatisticFieldsCalculatedTable::getInstance()->createQuery()->where('parent_field = ?', $this->getId())->execute();

			$calcSymbol = '';
			foreach($calFields as $field) {
				$fieldsResult[] = '( '.$field->getCalculatedField()->getHeader().' )';
				$calcSymbol = $field->getCalcType();
			}

			return implode('<strong>'.$this->getCalcSymbol($calcSymbol).'</strong>', $fieldsResult);
		}
		else if($this->getValueType() == self::FIELD_TYPE_TEXT)
			return '';

		return 'Значение';
	}

    /**
     * @param $activity
     * @param $user
     * @param $concept
     * @param null $year
     * @param null $quarter
     * @return ActivityExtendedStatisticFieldsData|string
     */
    public function getFieldUserValue ( $activity, $user, $concept, $year = null, $quarter = null )
    {
        $user = $user->getAuthUser();

        $userDealer = $user->getDealerUsers()->getFirst();
        $dealer = null;
        if ($userDealer)
            $dealer = DealerTable::getInstance()->createQuery('d')->where('id = ?', $userDealer->getDealerId())->fetchOne();

        if (!$dealer)
            return '';

        //$field = ActivityExtendedStatisticFieldsDataTable::getInstance()->createQuery()->where('field_id = ? and user_id = ? and dealer_id = ?', array($this->getId(), $user->getId(), $dealer->getId()))->fetchOne();
        $query = ActivityExtendedStatisticFieldsDataTable::getInstance()->createQuery()->where('field_id = ? and dealer_id = ?', array( $this->getId(), $dealer->getId() ));
        if ($concept) {
            $query->andWhere('concept_id = ?', $concept);
        }

        //Выборка с учетом года
        if (!is_null($year)) {
            $query->andWhere('year = ?', $year);
        }

        //Выборка с учетом квартала
        if (!is_null($quarter)) {
            $query->andWhere('quarter = ?', $quarter);
        }

        $field = $query->fetchOne();
        //$field = ActivityExtendedStatisticFieldsDataTable::getInstance()->createQuery()->where('field_id = ? and user_id = ?', array($this->getId(), $user->getId()))->fetchOne();
        if (!$field) {
            $field = new ActivityExtendedStatisticFieldsData();
            $field->setFieldId($this->getId());
            $field->setUserId($user->getId());
            $field->setDealerId($dealer->getId());
            $field->setActivityId($activity->getId());

            if (!is_null($year)) {
                $field->setYear($year);
            }

            if (!is_null($quarter)) {
                $field->setQuarter($quarter);
            }

            if ($concept)
                $field->setConceptId($concept);

            $val = '';
            if ($this->getValueType() == self::FIELD_TYPE_DATE)
                $val = sprintf('%s-%s', date('d.m.Y'), date('d.m.Y'));

            $field->setValue($val);
            $field->save();
        }

        return $field;
    }

    /**
     * @param $user
     * @param string $createdAt
     * @param int $conceptId
     * @return float|int|string
     */
    public function calculateValue($user, $createdAt = '', $conceptId = 0) {
		if(is_numeric($user)) {
            $dealerId = $user;
        }
		else {
            $dealerId = $user->getAuthUser()->getDealer()->getId();
        }

        //$calcFields = ActivityExtendedStatisticFieldsCalculatedTable::getInstance()->createQuery()->where('field_id = ? and user_id = ?', array($this->getId(), $user->getId()))->orderBy('order ASC')->execute();
		$calcFields = ActivityExtendedStatisticFieldsCalculatedTable::getInstance()->createQuery()->where('parent_field = ?', $this->getId())->orderBy('id ASC')->execute();

		$values = array();
		$calcType = '';

        foreach($calcFields as $field)
		{
			$calcType = $field->getCalcType();

			$checkField = ActivityExtendedStatisticFieldsTable::getInstance()->find($field->getCalcField());
			if($checkField && $checkField->getValueType() == self::FIELD_TYPE_CALC) {
				$values[] = $checkField->calculateValue($user, $createdAt, $conceptId);
			}
			else {
				$query = ActivityExtendedStatisticFieldsDataTable::getInstance()
                    ->createQuery()
                    ->where('field_id = ? and dealer_id = ?',
                        array(
                            $field->getCalcField(),
                            $dealerId
                        )
                    );

				if ($conceptId != 0) {
				    $query->andWhere('concept_id = ?', $conceptId);
                }

                if(!empty($createdAt)) {
                    $query->andWhere('created_at LIKE ?', $createdAt.'%');
                }

                $calcFields = $query->execute();
                if($calcFields) {
                    foreach($calcFields as $calcField) {
                        $values[] = $calcField->getValue();
                    }
				}
			}
		}

        if(!isset($values[0]) || !isset($values[1])) {
            return 0;
        }

        if(!is_numeric($values[0])) {
            $values[0] = intval($values[0]);
        }

        if(!is_numeric($values[1])) {
            $values[1] = intval($values[1]);
        }

        if($calcType == self::FIELD_CALC_SYMBOL_PLUS) {
            return $this->plusValues($values);
        }
		else if($calcType == self::FIELD_CALC_SYMBOL_MINUS) {
            return $this->minusValues($values);
        }
		else if($calcType == self::FIELD_CALC_SYMBOL_DIVIDE) {
			if($values[1] != 0) {
                return round($values[0] / $values[1], 2);
            }

			return 0;
		}
		else if($calcType == self::FIELD_CALC_SYMBOL_PERCENT) {
			return round($values[0] * 100 / $values[1], 2);
		}
	}

    /**
     * Сложение всех полей формулы
     * @param $values
     * @return int
     */
	private function plusValues($values) {
        $result = 0;

        if (!isset($values[0])) { return 0; }

        foreach ($values as $value) {
            $result += $value;
        }

        return $result;
    }

    /**
     * Вычитание всех полей формулы
     * @param $values
     * @return int
     */
    private function minusValues($values) {
        $result = 0;

        if (!isset($values[0])) { return 0; }

        foreach ($values as $value) {
            $result = $result == 0 ? $value : $result - $value;
        }

        return $result;
    }

	public function isCalcField() {
		return ActivityExtendedStatisticFieldsCalculatedTable::getInstance()->createQuery()->where('parent_field = ?', $this->getId())->orderBy('id ASC')->count() > 0 ? true : false;
	}

	public function getCalcFields() {
		$fields = array();
		$result = ActivityExtendedStatisticFieldsCalculatedTable::getInstance()->createQuery()->where('parent_field = ?', $this->getId())->orderBy('id ASC')->execute();

		foreach($result as $r)
			$fields[] = $r->getCalcField();

		return implode(":", $fields);
	}

	public function useInCalculate() {
		return ActivityExtendedStatisticFieldsCalculatedTable::getInstance()->createQuery()->where('calc_field = ?', $this->getId())->orderBy('id ASC')->count() > 0 ? true : false;
	}

	public function getCalculateSymbol() {
		$res = ActivityExtendedStatisticFieldsCalculatedTable::getInstance()->createQuery()->where('calc_field = ?', $this->getId())->orderBy('id ASC')->fetchOne();

		if(!$res)
			$res = ActivityExtendedStatisticFieldsCalculatedTable::getInstance()->createQuery()->where('parent_field = ?', $this->getId())->orderBy('id ASC')->fetchOne();

		return $res ? $res->getCalcType() : '';
	}

	public function getParentCalcField() {
		$field = ActivityExtendedStatisticFieldsCalculatedTable::getInstance()->createQuery()->where('calc_field = ?', $this->getId())->fetchOne();

		return $field ? $field->getParentField()->getId() : '0';
	}

	public function getCalcSymbol($symbol) {
		switch($symbol) {
			case self::FIELD_CALC_SYMBOL_PLUS:
				return '+';
			break;

			case self::FIELD_CALC_SYMBOL_MINUS:
				return '-';
			break;

			case self::FIELD_CALC_SYMBOL_DIVIDE:
				return '/';
			break;

			case self::FIELD_CALC_SYMBOL_PERCENT:
				return '%';
			break;
		}

		return '+';
	}

    /**
     * Save activity statistic data
     * @param sfWebRequest $request
     * @param $my_user
     * @param $files
     * @param null $activity
     * @return array
     * @internal param $user
     */
    public static function saveData ( sfWebRequest $request, $my_user, $files, $activity = null )
    {
        $items = json_decode($request->getParameter('txt_frm_fields_data'));
        $user = $my_user->getAuthUser();
        $result = array( 'success' => false, 'msg' => '' );

        //Получаем данные по импортеру
        $send_to = $request->getParameter('send_to');
        $to_importer = $is_importer = !empty($send_to) ? $request->getParameter('send_to') : '';

        //Получаем квартал / год
        $request_q = $request->getParameter('quarter');
        $request_q = !empty($request_q) && $request_q != 0 ? $request_q : D::getQuarter(time());

        $curr_quarter = $my_user->getCurrentQuarter() != 0 ? $my_user->getCurrentQuarter() : $request_q;
        $year = $my_user->getCurrentYear() != 0 ? $my_user->getCurrentYear() : D::getYear(D::calcQuarterData(date('d-m-Y')));

        //Получить данные по шагу
        $step_id = intval($request->getParameter("step_id"));
        $result[ 'step_id' ] = $step_id;

        $allowed_file_types = array( 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel' );
        $save_result = true;
        if (!empty($files)) {
            foreach ($files as $file_key => $file_data) {
                if (isset($file_data[ 'name' ]) && !empty($file_data[ 'name' ])) {
                    if (in_array($file_data[ 'type' ], $allowed_file_types)) {

                        $file_key_exploded = explode('_', $file_key);
                        $file_key_data = $file_key_exploded[ count($file_key_exploded) - 1 ];

                        $uniq_model = new UniqueFileNameGenerator(sfConfig::get('sf_upload_dir') . ActivityFields::FIELD_FILE_PATH);
                        $gen_file_name = $uniq_model->generate($file_data[ 'name' ]);

                        if (move_uploaded_file($file_data[ 'tmp_name' ], sfConfig::get('app_uploads_path') . ActivityFields::FIELD_FILE_PATH . '/' . $gen_file_name)) {
                            self::saveFieldData($request, $file_key_data, $gen_file_name, $user, $activity, $step_id, $curr_quarter, $year);

                            //Сохраняем данные загруженного файла по шагу
                            if ($step_id > 0) {
                                self::saveFieldDataByStep($request, $file_key_data, $gen_file_name, $user, $activity, $step_id, $curr_quarter, $year);
                            }

                            $save_result = true;
                        }
                    } else {
                        $save_result = false;
                        $result[ 'msg' ] = sprintF('%s (%s).', 'Неверный формат файла ', $file_data[ 'type' ]);
                    }
                }
            }
        }

        if ($step_id != 0) {
            foreach ($items as $key => $data) {
                self::saveFieldDataByStep($request, $data->id, $data->value, $user, $activity, $step_id, $curr_quarter, $year);

                $result[ 'success' ] = self::saveFieldData($request, $data->id, $data->value, $user, $activity, $step_id, $curr_quarter, $year);
            }

            //Проверяем на выполнение всех шагов привязанных к активности
            //Если все заполнено, завершаем статистику

            //Если не передалась активность, берем ее с концепции
            if (!$activity) {
                $concept = AgreementModelTable::getInstance()->createQuery()->where('id = ?', $request->getParameter('concept_id'))->fetchOne();
                if ($concept) {
                    $activity = $concept->getActivity();
                }
            }

            //Сохраняем данные по статусу заполненных данных
            //Если данные отправлены Импортеру, отмечаем выполнение шага
            $step_status = ActivityExtendedStatisticStepStatusTable::getInstance()->createQuery()
                ->where('step_id = ? and dealer_id = ? and concept_id = ? and year = ? and quarter = ? and activity_id = ?',
                    array
                    (
                        $step_id,
                        $user->getDealer()->getId(),
                        $request->getParameter('concept_id'),
                        $year,
                        $curr_quarter,
                        $activity->getId()
                    ))->fetchOne();

            $is_new = false;
            if (!$step_status) {

                $step_status = new ActivityExtendedStatisticStepStatus();
                $step_status->step_id = $step_id;
                $step_status->dealer_id = $user->getDealer()->getId();
                $step_status->concept_id = $request->getParameter('concept_id');
                $step_status->year = $year;
                $step_status->quarter = $curr_quarter;
                $step_status->status = false;
                $step_status->activity_id = $activity->getId();

                $step_status->save();
                $is_new = true;
            }

            if (!empty($to_importer) && !$is_new) {
                $step_status->year = $year;
                $step_status->quarter = $curr_quarter;
                $step_status->status = true;
                $step_status->save();
            }

            //Доп. проверка на наличие активности
            if ($activity) {
                $activity_steps = ActivityExtendedStatisticStepsTable::getInstance()->createQuery()->where('activity_id = ?', $activity->getId())->execute();

                $accepted = true;
                foreach ($activity_steps as $activity_step) {
                    $step_status = ActivityExtendedStatisticStepStatusTable::getInstance()->createQuery()
                        ->select('status')
                        ->where('step_id = ? and activity_id = ? and dealer_id = ? and concept_id = ? and year = ? and quarter = ?',
                            array
                            (
                                $activity_step->getId(),
                                $activity->getId(),
                                $user->getDealer()->getId(),
                                $request->getParameter('concept_id'),
                                $year,
                                $curr_quarter
                            ))->fetchOne();

                    if (!$step_status) {
                        $accepted = false;
                    } else if (!$step_status->getStatus()) {
                        $accepted = false;
                    }
                }
            }

            $result[ 'success' ] = $to_importer = $accepted;
        } else {
            foreach ($items as $key => $data) {
                $result[ 'success' ] = self::saveFieldData($request, $data->id, $data->value, $user, $activity, 0, $curr_quarter, $year);
            }
        }

        $query = ActivityDealerStaticticStatusTable::getInstance()->createQuery()->where('dealer_id = ? and activity_id = ? and year = ? and concept_id = ?',
            array
            (
                $user->getDealer()->getId(),
                $request->getParameter('activity'),
                $year,
                $request->getParameter('concept_id')
            )
        );

        $item = $query->fetchOne();
        if (!$item) {
            $item = new ActivityDealerStaticticStatus();
            $item->setArray(
                array
                (
                    'dealer_id' => $user->getDealer()->getId(),
                    'activity_id' => $request->getParameter('activity'),
                    'ignore_q' . $curr_quarter . '_statistic' => !$to_importer,
                    'stat_type' => Activity::ACTIVITY_STATISTIC_TYPE_EXTENDED,
                    'q' . $curr_quarter => $curr_quarter,
                    'year' => $year,
                    'complete' => $result[ 'success' ],
                    'concept_id' => $request->getParameter('concept_id'),
                    'using_steps' => $step_id != 0
                )
            );
        } else {
            $item->setArray(
                array
                (
                    'ignore_q' . $curr_quarter . '_statistic' => !$to_importer,
                    'q' . $curr_quarter => $curr_quarter,
                    'year' => $year,
                    'complete' => $result[ 'success' ],
                    'using_steps' => $step_id != 0
                )
            );
        }
        $item->save();

        $result[ 'allow_to_edit' ] = empty($is_importer);
        $result[ 'allow_to_cancel' ] = !empty($is_importer);
        $result[ 'success' ] = $save_result;
        $result[ 'importer' ] = $is_importer;

        return $result;
    }

    public static function saveFieldData ( sfWebRequest $request, $field_id, $field_value, $user, $activity, $step_id = 0, $quarter = 0, $year = 0 )
    {
        if (is_null($field_id)) {
            return true;
        }

        $field_data = ActivityExtendedStatisticFieldsDataTable::getInstance()
            ->createQuery()
            ->where('id = ? and dealer_id = ?', array( $field_id, $user->getDealer()->getId() ))
            ->fetchOne();

        $data_array = array();
        if (!$field_data) {
            $field_data = new ActivityExtendedStatisticFieldsData();
        }

        $data_array[ 'value' ] = $field_value;
        $data_array[ 'dealer_id' ] = $user->getDealer()->getId();
        $data_array[ 'concept_id' ] = $request->getParameter('concept_id');

        $data_array[ 'step_id' ] = $step_id;

        $data_array[ 'year' ] = $year;
        $data_array[ 'quarter' ] = $quarter;

        //$data_array[ 'field_id' ] = $field_id;

        if ($activity) {
            $data_array[ 'activity_id' ] = $activity->getId();
        }
        $data_array[ 'user_id' ] = $user->getId();

        $field_data->setArray($data_array);
        $field_data->save();

        return true;
    }

    private static function saveFieldDataByStep ( sfWebRequest $request, $field_id, $field_value, $user, $activity, $step_id, $quarter, $year )
    {
        if (is_null($field_id)) {
            return true;
        }

        $field_data = ActivityExtendedStatisticStepValuesTable::getInstance()
            ->createQuery()
            ->where('field_id = ? and dealer_id = ? and step_id = ? and concept_id = ?',
                array
                (
                    $field_id,
                    $user->getDealer()->getId(),
                    $step_id,
                    $request->getParameter('concept_id'),
                )
            )
            ->fetchOne();

        $data_array = array();
        if (!$field_data) {
            $field_data = new ActivityExtendedStatisticStepValues();
        }

        if ($field_id != 0) {
            //$data_array['field_id'] = $field_id;
        }

        $data_array[ 'step_id' ] = $step_id;
        $data_array[ 'value' ] = $field_value;
        $data_array[ 'dealer_id' ] = $user->getDealer()->getId();
        $data_array[ 'concept_id' ] = $request->getParameter('concept_id');
        $data_array[ 'year' ] = $year;
        $data_array[ 'quarter' ] = $quarter;

        if ($activity) {
            $data_array[ 'activity_id' ] = $activity->getId();
        }

        $field_data->setArray($data_array);
        $field_data->save();

        return true;
    }
}
