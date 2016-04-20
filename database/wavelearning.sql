drop database if exists wavelearningdb;
create database if not exists wavelearningdb 
	default character set utf8
	default collate utf8_general_ci;
use wavelearningdb;

create table users(
	id int not null primary key auto_increment,
	usertype boolean not null default 0,
	email varchar(60) not null,
	password char(32) not null,
	about text null
);

create table class (
	id int not null primary key auto_increment,
	name varchar(45) not null,
	token char(32) not null,
	description text not null
);

create table refclassuser (
	cid int not null,
	uid int not null
);
alter table refclassuser add constraint fk_class2 foreign key refclassuser (cid) references class (id)
	on update cascade on delete cascade;
alter table refclassuser add constraint fk_user1 foreign key refclassuser (uid) references users (id)
	on update cascade on delete cascade;

create table activity (
	id bigint not null primary key auto_increment,
	cid	int not null,
	adate date not null,
	name varchar(60) not null,
	description text not null
);
alter table activity add constraint fk_class1  foreign key activity (cid) references class (id)
	on update cascade on delete cascade;

create table activityupload (
	aid bigint not null,
	uid int not null,
	content text not null
);
alter table activityupload add constraint fk_activity0 foreign key activityupload (aid) references activity (id)
	on update cascade on delete cascade;
alter table activityupload add constraint fk_user3 foreign key activityupload (uid) references users (id)
	on update cascade on delete cascade;
	
delimiter //
	create trigger trgBIusers before insert on users
	for each row
	begin
		set new.password = (select md5(new.password));
	end;//
	
	create trigger trgBIClass before insert on class
	for each row
	begin
		set new.token = (select md5(concat((rand()*rand()*1965487856), "[&&%%[{]]]&&}]]")));
	end;//
delimiter ;

/*
	DUMPS
*/
insert into users(usertype, email, password) values(1, "developerdiego0@gmail.com", "diego325X");
insert into users(usertype, email, password) values(0, "alex0234@gmail.com", "alex252Kl");
insert into class(name, description) values("Dinámica Vectorial", "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
	tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
	quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
	consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
	cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
	proident, sunt in culpa qui officia deserunt mollit anim id est laborum."),
	("Fisica cuántica", "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
	tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
	quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
	consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
	cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
	proident, sunt in culpa qui officia deserunt mollit anim id est laborum."),
	("Dinámica de partículas", "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
	tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
	quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
	consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
	cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
	proident, sunt in culpa qui officia deserunt mollit anim id est laborum."),
	("Programación paralela", "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
	tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
	quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
	consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
	cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
	proident, sunt in culpa qui officia deserunt mollit anim id est laborum."),
	("Visión artificial", "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
	tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
	quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
	consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
	cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
	proident, sunt in culpa qui officia deserunt mollit anim id est laborum.");

insert into activity(cid,adate,name,description) select 1, date(now()), "Introducción", "Lorem ipsum dolor sit amet, consectetur adipisicing elit. Veritatis fuga, harum, consequuntur recusandae porro eaque praesentium, accusamus, mollitia et excepturi ipsum hic! Vitae non quia labore laboriosam provident ipsa necessitatibus.";
insert into activity(cid,adate,name,description) select 2, date(now()), "Introducción", "Lorem ipsum dolor sit amet, consectetur adipisicing elit. Veritatis fuga, harum, consequuntur recusandae porro eaque praesentium, accusamus, mollitia et excepturi ipsum hic! Vitae non quia labore laboriosam provident ipsa necessitatibus.";
insert into activity(cid,adate,name,description) select 3, date(now()), "Introducción", "Lorem ipsum dolor sit amet, consectetur adipisicing elit. Veritatis fuga, harum, consequuntur recusandae porro eaque praesentium, accusamus, mollitia et excepturi ipsum hic! Vitae non quia labore laboriosam provident ipsa necessitatibus.";
insert into activity(cid,adate,name,description) select 4, date(now()), "Introducción", "Lorem ipsum dolor sit amet, consectetur adipisicing elit. Veritatis fuga, harum, consequuntur recusandae porro eaque praesentium, accusamus, mollitia et excepturi ipsum hic! Vitae non quia labore laboriosam provident ipsa necessitatibus.";
insert into activity(cid,adate,name,description) select 5, date(now()), "Introducción", "Lorem ipsum dolor sit amet, consectetur adipisicing elit. Veritatis fuga, harum, consequuntur recusandae porro eaque praesentium, accusamus, mollitia et excepturi ipsum hic! Vitae non quia labore laboriosam provident ipsa necessitatibus.";