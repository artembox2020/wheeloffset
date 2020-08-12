<?php

class m200313_150156_product_category_texts extends CDbMigration
{
	public function up()
	{
        $sql = "
        ALTER TABLE `product_category_model`  
        ADD `text_pros_id` INT(11) UNSIGNED NULL DEFAULT NULL  AFTER `footer_text`,  
        ADD `text_cons_id` INT(11) UNSIGNED NULL DEFAULT NULL  AFTER `text_pros_id`;
        CREATE TABLE `product_category_text` (
          `id` int(11) UNSIGNED NOT NULL,
          `category_id` int(11) UNSIGNED NOT NULL,
          `text` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
          `number` tinyint(2) UNSIGNED NOT NULL,
          `type` tinyint(1) NOT NULL COMMENT '1 - pros, 2 - cons'
        ) ENGINE=InnoDB;
        ALTER TABLE `product_category_text`
          ADD PRIMARY KEY (`id`),
          ADD KEY `category_id` (`category_id`);
        ALTER TABLE `product_category_text`
          MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
        ALTER TABLE `product_category_text`
          ADD CONSTRAINT `product_category_text_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `product_category` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;
            ";

        try {
            Yii::app()->db->createCommand($sql)->execute();
        } catch (Exception $e) {
            echo 'DB exception: ',  $e->getMessage(), "\n";
        }                
	}

	public function down()
	{
		echo "m200313_150156_product_category_texts does not support migration down.\n";
		return false;
	}
}