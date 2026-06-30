create database bd_mundo;
use bd_mundo;

create table governantes (
    id_governante int auto_increment primary key,
    nome varchar(150) not null,
    partido_politico varchar(100),
    data_nascimento date,
    idade int,
    data_inicio_mandato date,
    data_fim_mandato date
);

create table continentes (
    id_continente int auto_increment primary key,
    nome varchar(100) not null,
    populacao bigint,
    area decimal(15,2),
    total_paises int
);

create table paises (
    id_pais int auto_increment primary key,
    nome varchar(150) not null,
    id_continente int,
    populacao bigint,
    area decimal(15,2),
    idioma varchar(100),
    id_governante int,
    clima varchar(100),
    regime_politico varchar(100),
    moeda varchar(100),

    foreign key (id_continente) references continentes(id_continente),
    foreign key (id_governante) references governantes(id_governante)
);

create table cidades (
    id_cidade int auto_increment primary key,
    nome varchar(150) not null,
    id_pais int,
    populacao bigint,
    area decimal(15,2),
    clima varchar(100),
    id_governante int,
    data_fundacao date,

    foreign key (id_pais) references paises(id_pais),
    foreign key (id_governante) references governantes(id_governante)
);

delimiter $$

create trigger atualizar_total_paises_insert
after insert on paises
for each row
begin
    update continentes
    set total_paises = (
        select count(*)
        from paises
        where id_continente = new.id_continente
    )
    where id_continente = new.id_continente;
end$$

create trigger atualizar_total_paises_delete
after delete on paises
for each row
begin
    update continentes
    set total_paises = (
        select count(*)
        from paises
        where id_continente = old.id_continente
    )
    where id_continente = old.id_continente;
end$$

create trigger atualizar_total_paises_update
after update on paises
for each row
begin
    if old.id_continente <> new.id_continente then

        update continentes
        set total_paises = (
            select count(*)
            from paises
            where id_continente = old.id_continente
        )
        where id_continente = old.id_continente;

        update continentes
        set total_paises = (
            select count(*)
            from paises
            where id_continente = new.id_continente
        )
        where id_continente = new.id_continente;

    end if;
end$$

delimiter ;

insert into continentes (nome, populacao, area, total_paises) values
('américa do sul', 430000000, 17840000.00, 0),
('europa', 747000000, 10180000.00, 0),
('ásia', 4700000000, 44579000.00, 0);

insert into governantes (nome, partido_politico, data_nascimento, idade, data_inicio_mandato, data_fim_mandato) values
('luiz inácio lula da silva', 'pt', '1945-10-27', 80, '2023-01-01', '2026-12-31'),
('emmanuel macron', 'renaissance', '1977-12-21', 48, '2022-05-14', '2027-05-13');

insert into paises (nome, id_continente, populacao, area, idioma, id_governante, clima, regime_politico, moeda) values
('brasil', 1, 203000000, 8515767.00, 'português', 1, 'tropical', 'república presidencialista', 'real'),
('frança', 2, 67000000, 551695.00, 'francês', 2, 'temperado', 'república semipresidencialista', 'euro');

insert into cidades (nome, id_pais, populacao, area, clima, id_governante, data_fundacao) values
('são paulo', 1, 12300000, 1521.11, 'subtropical', null, '1554-01-25'),
('rio de janeiro', 1, 6700000, 1200.27, 'tropical', null, '1565-03-01'),
('paris', 2, 2140000, 105.40, 'oceânico', null, '0508-01-01');