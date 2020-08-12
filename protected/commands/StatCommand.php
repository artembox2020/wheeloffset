<?php
class StatCommand extends CConsoleCommand
{

	public function init() 
	{
		date_default_timezone_set("Europe/Moscow");
		ini_set('max_execution_time', 3600*12);
		return parent::init();
	}

	public function actionProject()
	{	
		//all stats
		$time 			= strtotime('-1 day');
		$dayTimeFrom 	= strtotime(date('Y-m-d 00:00:00', $time));
		$monthTimeFrom 	= strtotime(date('Y-m-01 00:00:00', $time));
		$timeTo 		= strtotime(date('Y-m-d 23:59:58', $time));
		
		$criteria=new CDbCriteria;
		$criteria->condition = "create_time >= {$dayTimeFrom} AND create_time <= {$timeTo}";
		
		$model = new ProjectStat;
		$model->total_day = Project::model()->count($criteria);

		$criteria=new CDbCriteria;
		$criteria->condition = "create_time >= {$monthTimeFrom} AND create_time <= {$timeTo}";
		$model->total_month = Project::model()->count($criteria);
		
		$criteria=new CDbCriteria;
		$criteria->condition = "create_time <= {$timeTo}";
		$model->total = Project::model()->count($criteria);
		
		$model->date = date('Y-m-d', $time);
		$model->save();		
		
		//user stats
		$users = Admin::model()->findAll();
		foreach ($users as $user) {
			$time 			= strtotime('-1 day');
			$dayTimeFrom 	= strtotime(date('Y-m-d 00:00:00', $time));
			$monthTimeFrom 	= strtotime(date('Y-m-01 00:00:00', $time));
			$timeTo 		= strtotime(date('Y-m-d 23:59:58', $time));
			
			$criteria=new CDbCriteria;
			$criteria->condition = "create_time >= {$dayTimeFrom} AND create_time <= {$timeTo} AND user_id = {$user->id}";
			
			$model = new ProjectStatUser;
			$model->user_id 	= $user->id;
			$model->total_day 	= Project::model()->count($criteria);

			$criteria=new CDbCriteria;
			$criteria->condition = "create_time >= {$monthTimeFrom} AND create_time <= {$timeTo}  AND user_id = {$user->id}";
			$model->total_month = Project::model()->count($criteria);
			
			$criteria=new CDbCriteria;
			$criteria->condition = "create_time <= {$timeTo} AND user_id = {$user->id}";
			$model->total = Project::model()->count($criteria);
			
			$model->date = date('Y-m-d', $time);
			
			if ($model->total_day || $model->total_month || $model->total)
			$model->save();
		}
	}
	
	public function actionSet()
	{	
		$time = time();
		$projects = Project::model()->findAll();
		foreach ($projects as $project) {
			$project->create_time = $time;
			$project->save(false);
			
			echo "{$project->id} {$project->create_time} \n";
			
			$time-= 60*60*24;
		}
		
	}
	
}