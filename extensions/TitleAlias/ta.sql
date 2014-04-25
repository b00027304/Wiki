create table /*$wgDBprefix*/titlealias( 
        title varchar(255),
        alias varchar(255),
        UNIQUE Key (title)
) /*$wgDBTableOptions*/;
