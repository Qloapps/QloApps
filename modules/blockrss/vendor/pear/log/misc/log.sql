-- $Id$

CREATE TABLE log_table (
    id          INT NOT NULL,
    logtime     TIMESTAMP NOT NULL,
    ident       CHAR(16) NOT NULL,
    priority    INT NOT NULL,
    message     VARCHAR(200),
    PRIMARY KEY (id)
);
