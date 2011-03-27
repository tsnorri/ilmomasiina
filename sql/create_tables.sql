SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO,ANSI_QUOTES,ERROR_FOR_DIVISION_BY_ZERO,STRICT_ALL_TABLES,TRADITIONAL,NO_ENGINE_SUBSTITUTION,NO_ZERO_DATE,NO_ZERO_IN_DATE,PIPES_AS_CONCAT,REAL_AS_FLOAT';


CREATE TABLE "ilmo_machine" (
	"id" BIGINT PRIMARY KEY auto_increment,
	"opens" DATETIME NOT NULL,
	"closes" DATETIME NOT NULL,
	"title" VARCHAR(255) NOT NULL,
	"description" TEXT,
	"eventdate" DATETIME NOT NULL,
	"password" varchar(8) default NULL, -- FIXME cleartext passwords?
	"send_confirmation" BIT(1) NOT NULL DEFAULT b'0',
	"confirmation_message" TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE "ilmo_question" (
	"id" BIGINT PRIMARY KEY auto_increment,
	"machine_id" BIGINT NOT NULL,
	"question" TEXT NOT NULL,
	"type" TEXT NOT NULL,
	"options" TEXT NOT NULL,
	"public" BIT(1) NOT NULL default b'0',
	"required" BIT(1) NOT NULL default b'0',
	CONSTRAINT "ilmo_machine_fkey" FOREIGN KEY ("machine_id") REFERENCES "ilmo_machine" ("id") ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE "ilmo_user" (
	"id" BIGINT PRIMARY KEY auto_increment,
	"machine_id" BIGINT NOT NULL,
	"id_string" VARCHAR(255) NOT NULL, -- FIXME what is this?
	"time" TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	"confirmed" BIT(1) NOT NULL DEFAULT b'0',
	CONSTRAINT "ilmo_machine_fkey" FOREIGN KEY ("machine_id") REFERENCES "ilmo_machine" ("id") ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE "ilmo_answer" (
	"id" BIGINT PRIMARY KEY auto_increment,
	"user_id" BIGINT NOT NULL,
	"question_id" BIGINT NOT NULL,
	"answer" TEXT DEFAULT NULL,
	CONSTRAINT "ilmo_user_fkey" FOREIGN KEY ("user_id") REFERENCES "ilmo_user" ("id") ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT "ilmo_question_fkey" FOREIGN KEY ("question_id") REFERENCES "ilmo_question" ("id") ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
