<?php
defined ( '_JEXEC' ) or die ();
//
JLoader::register ( 'SiteTalentHelper', JPATH_SITE . '/components/com_talent/helpers/talent.php' );
//
class ModTalentSearchHelper {
	//
	protected static $params;
	//
	protected static $fields = array (
			'name',
			'email',
			'race',
			'location',
			'hair_color',
			'eye_color',
			'height',
			'weight',
			'chest',
			'waist',
			'hip',
			'gender' 
	);
	//
	protected static $filter_fields = array ();
	//
	protected static $state;
	//
	protected static $context = 'com_talent.talents';
	//
	public static function getDisplayData($params) {
		static::$params = $params;
		static::$state = new JObject ();
		static::populateState ();
		$view = new stdClass ();
		$view->state = static::getState ();
		$view->filterForm = static::getForm ();
		static::$filter_fields = array ();
		foreach ( static::$fields as $field ) {
			$enable = ( int ) static::$params->get ( 'enable_' . $field, 0 );
			if ($enable) {
				array_push ( static::$filter_fields, 'filter_' . $field );
			}
		}
		$view->filter_fields = static::$filter_fields;
		return $view;
	}
	//
	public static function getForm() {
		// Get the form.
		JForm::addFormPath ( JPATH_SITE . '/modules/mod_talentsearch/forms' );
		// JForm::addFormPath ( JPATH_SITE . '/com_talent/models/fields' );
		$form = JForm::getInstance ( static::$context, 'filter_talents', array (
				'control' => '',
				'load_data' => 1 
		), false, false );
		$data = static::loadFormData ();
		static::preprocessForm ( $form, $data );
		$form->bind ( $data );
		return $form;
	}
	//
	public static function loadFormData() {
		$data = JFactory::getApplication ()->getUserState ( static::$context, new stdClass () );
		return $data;
	}
	//
	public static function preprocessForm(JForm $form, $data, $group = 'content') {
		JPluginHelper::importPlugin ( $group );
		$dispatcher = JDispatcher::getInstance ();
		$results = $dispatcher->trigger ( 'onContentPrepareForm', array (
				$form,
				$data 
		) );
		// Check for errors encountered while preparing the form.
		if (count ( $results ) && in_array ( false, $results, true )) {
			// Get the last error.
			$error = $dispatcher->getError ();
			if (! ($error instanceof Exception)) {
				throw new Exception ( $error );
			}
		}
	}
	//
	public static function getState($property = null, $default = null) {
		return $property === null ? static::$state : static::$state->get ( $property, $default );
	}
	//
	public static function setState($property, $value = null) {
		return static::$state->set ( $property, $value );
	}
	//
	protected static function populateState($ordering = null, $direction = null) {
		$app = JFactory::getApplication ();
		// Receive & set filters
		if ($filters = $app->getUserStateFromRequest ( static::$context . '.filter', 'filter', array (), 'array' )) {
			foreach ( $filters as $name => $value ) {
				static::setState ( 'filter.' . $name, null );
			}
		}
		$limit = 0;
		// Receive & set list options
		if ($list = $app->getUserStateFromRequest ( static::$context . '.list', 'list', array (), 'array' )) {
			foreach ( $list as $name => $value ) {
				// Extra validations
				switch ($name) {
					case 'fullordering' :
						$orderingParts = explode ( ' ', $value );
						if (count ( $orderingParts ) >= 2) {
							// Latest part will be considered the direction
							$fullDirection = end ( $orderingParts );
							if (in_array ( strtoupper ( $fullDirection ), array (
									'ASC',
									'DESC',
									'' 
							) )) {
								static::setState ( 'list.direction', $fullDirection );
							}
							unset ( $orderingParts [count ( $orderingParts ) - 1] );
							// The rest will be the ordering
							$fullOrdering = implode ( ' ', $orderingParts );
							if (in_array ( $fullOrdering, static::$filter_fields )) {
								static::setState ( 'list.ordering', $fullOrdering );
							}
						} else {
							static::setState ( 'list.ordering', $ordering );
							static::setState ( 'list.direction', $direction );
						}
						break;
					case 'ordering' :
						if (! in_array ( $value, static::$filter_fields )) {
							$value = $ordering;
						}
						break;
					case 'direction' :
						if (! in_array ( strtoupper ( $value ), array (
								'ASC',
								'DESC',
								'' 
						) )) {
							$value = $direction;
						}
						break;
					case 'limit' :
						$limit = $value;
						break;
					// Just to keep the default case
					default :
						$value = $value;
						break;
				}
				static::setState ( 'list.' . $name, $value );
			}
		} else { // Keep B/C for components previous to jform forms for filters
			$limit = $app->getUserStateFromRequest ( 'global.list.limit', 'limit', $app->get ( 'list_limit' ), 'uint' );
			static::setState ( 'list.limit', $limit );
			// Check if the ordering field is in the white list, otherwise use the incoming value.
			$value = $app->getUserStateFromRequest ( static::$context . '.ordercol', 'filter_order', $ordering );
			if (! in_array ( $value, static::$filter_fields )) {
				$value = $ordering;
				$app->setUserState ( static::$context . '.ordercol', $value );
			}
			static::setState ( 'list.ordering', $value );
			// Check if the ordering direction is valid, otherwise use the incoming value.
			$value = $app->getUserStateFromRequest ( static::$context . '.orderdirn', 'filter_order_Dir', $direction );
			if (! in_array ( strtoupper ( $value ), array (
					'ASC',
					'DESC',
					'' 
			) )) {
				$value = $direction;
				$app->setUserState ( static::$context . '.orderdirn', $value );
			}
			static::setState ( 'list.direction', $value );
		}
		// Support old ordering field
		$oldOrdering = $app->input->get ( 'filter_order' );
		if (! empty ( $oldOrdering ) && in_array ( $oldOrdering, static::$filter_fields )) {
			static::setState ( 'list.ordering', $oldOrdering );
		}
		// Support old direction field
		$oldDirection = $app->input->get ( 'filter_order_Dir' );
		if (! empty ( $oldDirection ) && in_array ( strtoupper ( $oldDirection ), array (
				'ASC',
				'DESC',
				'' 
		) )) {
			static::setState ( 'list.direction', $oldDirection );
		}
		$value = $app->getUserStateFromRequest ( static::$context . '.limitstart', 'limitstart', 0 );
		$limitstart = ($limit != 0 ? (floor ( $value / $limit ) * $limit) : 0);
		static::setState ( 'list.start', $limitstart );
		$app->setUserState ( static::$context, static::$state );
	}
}
