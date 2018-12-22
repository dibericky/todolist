# todolist

![Architecture Study](ToDoListStudy.png)

### User 
    CREATE TABLE `my_db`.`User` ( `id` INT NOT NULL AUTO_INCREMENT , `nickname` VARCHAR(255) NOT NULL , `password` VARCHAR(255) NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

### Task  
    CREATE TABLE `my_db`.`Task` (
        `id` INT(10) NOT NULL AUTO_INCREMENT , 
        `state` INT(1) NOT NULL DEFAULT '1' COMMENT 'todo 1, running 2, done 3' ,
        `date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , 
        `title` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL , 
        `description` TEXT CHARACTER SET utf8 COLLATE utf8_bin NOT NULL , 
        `userId` INT NOT NULL , 
        PRIMARY KEY (`id`),
        FOREIGN KEY (`userId`) REFERENCES user(`id`)
    ) ENGINE = InnoDB;

   
