/****** Object:  Database Documentos    Script Date: 06/11/2009 14:34:12 ******/
IF EXISTS (SELECT name FROM master.dbo.sysdatabases WHERE name = N'Documentos')
	DROP DATABASE [Documentos]
GO

CREATE DATABASE [Documentos]  ON (NAME = N'Documentos_Data', FILENAME = N'e:\MSSQL\MSSQL\Data\Documentos_Data.MDF' , SIZE = 2, FILEGROWTH = 10%) LOG ON (NAME = N'Documentos_Log', FILENAME = N'e:\MSSQL\MSSQL\Data\Documentos_Log.LDF' , SIZE = 1, FILEGROWTH = 10%)
GO

exec sp_dboption N'Documentos', N'autoclose', N'false'
GO

exec sp_dboption N'Documentos', N'bulkcopy', N'false'
GO

exec sp_dboption N'Documentos', N'trunc. log', N'true'
GO

exec sp_dboption N'Documentos', N'torn page detection', N'true'
GO

exec sp_dboption N'Documentos', N'read only', N'false'
GO

exec sp_dboption N'Documentos', N'dbo use', N'false'
GO

exec sp_dboption N'Documentos', N'single', N'false'
GO

exec sp_dboption N'Documentos', N'autoshrink', N'true'
GO

exec sp_dboption N'Documentos', N'ANSI null default', N'false'
GO

exec sp_dboption N'Documentos', N'recursive triggers', N'false'
GO

exec sp_dboption N'Documentos', N'ANSI nulls', N'false'
GO

exec sp_dboption N'Documentos', N'concat null yields null', N'false'
GO

exec sp_dboption N'Documentos', N'cursor close on commit', N'false'
GO

exec sp_dboption N'Documentos', N'default to local cursor', N'false'
GO

exec sp_dboption N'Documentos', N'quoted identifier', N'false'
GO

exec sp_dboption N'Documentos', N'ANSI warnings', N'false'
GO

exec sp_dboption N'Documentos', N'auto create statistics', N'true'
GO

exec sp_dboption N'Documentos', N'auto update statistics', N'true'
GO

use [Documentos]
GO

if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[FK_tb_usuario_tb_tipo_usuario]') and OBJECTPROPERTY(id, N'IsForeignKey') = 1)
ALTER TABLE [dbo].[tb_usuario] DROP CONSTRAINT FK_tb_usuario_tb_tipo_usuario
GO

if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[FK_tb_tramitacao_tb_unidade]') and OBJECTPROPERTY(id, N'IsForeignKey') = 1)
ALTER TABLE [dbo].[tb_tramitacao] DROP CONSTRAINT FK_tb_tramitacao_tb_unidade
GO

if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[FK_tb_usuario_tb_unidade]') and OBJECTPROPERTY(id, N'IsForeignKey') = 1)
ALTER TABLE [dbo].[tb_usuario] DROP CONSTRAINT FK_tb_usuario_tb_unidade
GO

if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[FK_tb_assunto_tb_usuario]') and OBJECTPROPERTY(id, N'IsForeignKey') = 1)
ALTER TABLE [dbo].[tb_assunto] DROP CONSTRAINT FK_tb_assunto_tb_usuario
GO

if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[FK_tb_documento_tb_usuario]') and OBJECTPROPERTY(id, N'IsForeignKey') = 1)
ALTER TABLE [dbo].[tb_documento] DROP CONSTRAINT FK_tb_documento_tb_usuario
GO

if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[FK_tb_orgao_externo_tb_usuario]') and OBJECTPROPERTY(id, N'IsForeignKey') = 1)
ALTER TABLE [dbo].[tb_orgao_externo] DROP CONSTRAINT FK_tb_orgao_externo_tb_usuario
GO

if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[FK_tb_tipo_documento_tb_usuario]') and OBJECTPROPERTY(id, N'IsForeignKey') = 1)
ALTER TABLE [dbo].[tb_tipo_documento] DROP CONSTRAINT FK_tb_tipo_documento_tb_usuario
GO

if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[FK_tb_tramitacao_tb_usuario]') and OBJECTPROPERTY(id, N'IsForeignKey') = 1)
ALTER TABLE [dbo].[tb_tramitacao] DROP CONSTRAINT FK_tb_tramitacao_tb_usuario
GO

if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[FK_Documento_Assunto]') and OBJECTPROPERTY(id, N'IsForeignKey') = 1)
ALTER TABLE [dbo].[tb_documento] DROP CONSTRAINT FK_Documento_Assunto
GO

if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[FK_Documento_OrgaoExterno]') and OBJECTPROPERTY(id, N'IsForeignKey') = 1)
ALTER TABLE [dbo].[tb_documento] DROP CONSTRAINT FK_Documento_OrgaoExterno
GO

if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[FK_Documento_TipoDocumento]') and OBJECTPROPERTY(id, N'IsForeignKey') = 1)
ALTER TABLE [dbo].[tb_documento] DROP CONSTRAINT FK_Documento_TipoDocumento
GO

if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[FK_tb_tramitacao_tb_documento]') and OBJECTPROPERTY(id, N'IsForeignKey') = 1)
ALTER TABLE [dbo].[tb_tramitacao] DROP CONSTRAINT FK_tb_tramitacao_tb_documento
GO

/****** Object:  Table [dbo].[tb_tramitacao]    Script Date: 06/11/2009 14:34:13 ******/
if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[tb_tramitacao]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)
drop table [dbo].[tb_tramitacao]
GO

/****** Object:  Table [dbo].[tb_documento]    Script Date: 06/11/2009 14:34:13 ******/
if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[tb_documento]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)
drop table [dbo].[tb_documento]
GO

/****** Object:  Table [dbo].[tb_assunto]    Script Date: 06/11/2009 14:34:13 ******/
if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[tb_assunto]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)
drop table [dbo].[tb_assunto]
GO

/****** Object:  Table [dbo].[tb_orgao_externo]    Script Date: 06/11/2009 14:34:13 ******/
if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[tb_orgao_externo]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)
drop table [dbo].[tb_orgao_externo]
GO

/****** Object:  Table [dbo].[tb_tipo_documento]    Script Date: 06/11/2009 14:34:13 ******/
if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[tb_tipo_documento]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)
drop table [dbo].[tb_tipo_documento]
GO

/****** Object:  Table [dbo].[tb_usuario]    Script Date: 06/11/2009 14:34:13 ******/
if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[tb_usuario]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)
drop table [dbo].[tb_usuario]
GO

/****** Object:  Table [dbo].[tb_tipo_usuario]    Script Date: 06/11/2009 14:34:13 ******/
if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[tb_tipo_usuario]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)
drop table [dbo].[tb_tipo_usuario]
GO

/****** Object:  Table [dbo].[tb_unidade]    Script Date: 06/11/2009 14:34:13 ******/
if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[tb_unidade]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)
drop table [dbo].[tb_unidade]
GO

/****** Object:  Login distributor_admin    Script Date: 06/11/2009 14:34:12 ******/
if not exists (select * from master.dbo.syslogins where loginname = N'distributor_admin')
BEGIN
	declare @logindb nvarchar(132), @loginlang nvarchar(132) select @logindb = N'master', @loginlang = N'us_english'
	if @logindb is null or not exists (select * from master.dbo.sysdatabases where name = @logindb)
		select @logindb = N'master'
	if @loginlang is null or (not exists (select * from master.dbo.syslanguages where name = @loginlang) and @loginlang <> N'us_english')
		select @loginlang = @@language
	exec sp_addlogin N'distributor_admin', null, @logindb, @loginlang
END
GO

/****** Object:  Login BUILTIN\Administradores    Script Date: 06/11/2009 14:34:12 ******/
exec sp_addsrvrolemember N'BUILTIN\Administradores', sysadmin
GO

/****** Object:  Login distributor_admin    Script Date: 06/11/2009 14:34:12 ******/
exec sp_addsrvrolemember N'distributor_admin', sysadmin
GO

/****** Object:  Table [dbo].[tb_tipo_usuario]    Script Date: 06/11/2009 14:34:13 ******/
CREATE TABLE [dbo].[tb_tipo_usuario] (
	[tu_id] [int] IDENTITY (1, 1) NOT NULL ,
	[tu_descricao] [varchar] (50) NOT NULL 
) ON [PRIMARY]
GO

/****** Object:  Table [dbo].[tb_unidade]    Script Date: 06/11/2009 14:34:14 ******/
CREATE TABLE [dbo].[tb_unidade] (
	[un_id] [int] IDENTITY (1, 1) NOT NULL ,
	[un_descricao] [varchar] (100) NOT NULL 
) ON [PRIMARY]
GO

/****** Object:  Table [dbo].[tb_usuario]    Script Date: 06/11/2009 14:34:14 ******/
CREATE TABLE [dbo].[tb_usuario] (
	[us_id] [int] IDENTITY (1, 1) NOT NULL ,
	[us_nome] [varchar] (80) NOT NULL ,
	[us_login] [varchar] (18) NOT NULL ,
	[us_senha] [varchar] (20) NOT NULL ,
	[un_id] [int] NOT NULL ,
	[tu_id] [int] NOT NULL 
) ON [PRIMARY]
GO

/****** Object:  Table [dbo].[tb_assunto]    Script Date: 06/11/2009 14:34:14 ******/
CREATE TABLE [dbo].[tb_assunto] (
	[as_id] [int] IDENTITY (1, 1) NOT NULL ,
	[as_descricao] [varchar] (150) NOT NULL ,
	[as_data_cadastro] [datetime] NOT NULL ,
	[us_id] [int] NOT NULL 
) ON [PRIMARY]
GO

/****** Object:  Table [dbo].[tb_orgao_externo]    Script Date: 06/11/2009 14:34:14 ******/
CREATE TABLE [dbo].[tb_orgao_externo] (
	[oe_id] [int] IDENTITY (1, 1) NOT NULL ,
	[oe_descricao] [varchar] (150) NOT NULL ,
	[oe_data_cadastro] [datetime] NOT NULL ,
	[us_id] [int] NOT NULL 
) ON [PRIMARY]
GO

/****** Object:  Table [dbo].[tb_tipo_documento]    Script Date: 06/11/2009 14:34:14 ******/
CREATE TABLE [dbo].[tb_tipo_documento] (
	[td_id] [int] IDENTITY (1, 1) NOT NULL ,
	[td_descricao] [varchar] (150) NOT NULL ,
	[td_data_cadastro] [datetime] NOT NULL ,
	[us_id] [int] NOT NULL 
) ON [PRIMARY]
GO

/****** Object:  Table [dbo].[tb_documento]    Script Date: 06/11/2009 14:34:14 ******/
CREATE TABLE [dbo].[tb_documento] (
	[dc_id] [int] IDENTITY (1, 1) NOT NULL ,
	[td_id] [int] NOT NULL ,
	[us_id] [int] NOT NULL ,
	[dc_data_elaboracao] [datetime] NOT NULL ,
	[as_id] [int] NOT NULL ,
	[dc_compl_assunto] [varchar] (150) NULL ,
	[oe_id] [int] NULL 
) ON [PRIMARY]
GO

/****** Object:  Table [dbo].[tb_tramitacao]    Script Date: 06/11/2009 14:34:14 ******/
CREATE TABLE [dbo].[tb_tramitacao] (
	[tr_id] [int] IDENTITY (1, 1) NOT NULL ,
	[dc_id] [int] NOT NULL ,
	[td_id] [int] NOT NULL ,
	[tr_data_inicio] [datetime] NOT NULL ,
	[tr_data_termino] [datetime] NULL ,
	[tr_cota] [varchar] (250) NULL ,
	[us_id] [int] NOT NULL ,
	[un_id] [int] NOT NULL 
) ON [PRIMARY]
GO

ALTER TABLE [dbo].[tb_tipo_usuario] WITH NOCHECK ADD 
	CONSTRAINT [PK_TipoUsuario] PRIMARY KEY  CLUSTERED 
	(
		[tu_id]
	)  ON [PRIMARY] 
GO

ALTER TABLE [dbo].[tb_unidade] WITH NOCHECK ADD 
	CONSTRAINT [PK_tb_unidade] PRIMARY KEY  CLUSTERED 
	(
		[un_id]
	)  ON [PRIMARY] 
GO

ALTER TABLE [dbo].[tb_usuario] WITH NOCHECK ADD 
	CONSTRAINT [PK_tb_usuario] PRIMARY KEY  CLUSTERED 
	(
		[us_id]
	)  ON [PRIMARY] 
GO

ALTER TABLE [dbo].[tb_assunto] WITH NOCHECK ADD 
	CONSTRAINT [PK_Assunto] PRIMARY KEY  CLUSTERED 
	(
		[as_id]
	)  ON [PRIMARY] 
GO

ALTER TABLE [dbo].[tb_orgao_externo] WITH NOCHECK ADD 
	CONSTRAINT [PK_OrgaoExterno] PRIMARY KEY  CLUSTERED 
	(
		[oe_id]
	)  ON [PRIMARY] 
GO

ALTER TABLE [dbo].[tb_tipo_documento] WITH NOCHECK ADD 
	CONSTRAINT [PK_TipoDocumento] PRIMARY KEY  CLUSTERED 
	(
		[td_id]
	)  ON [PRIMARY] 
GO

ALTER TABLE [dbo].[tb_documento] WITH NOCHECK ADD 
	CONSTRAINT [PK_tb_documento] PRIMARY KEY  CLUSTERED 
	(
		[dc_id]
	)  ON [PRIMARY] 
GO

ALTER TABLE [dbo].[tb_tramitacao] WITH NOCHECK ADD 
	CONSTRAINT [PK_Tramitacao] PRIMARY KEY  CLUSTERED 
	(
		[tr_id]
	)  ON [PRIMARY] 
GO

 CREATE  UNIQUE  INDEX [IX_tb_tramitacao] ON [dbo].[tb_tramitacao]([dc_id], [td_id], [tr_data_inicio]) ON [PRIMARY]
GO

ALTER TABLE [dbo].[tb_usuario] ADD 
	CONSTRAINT [FK_tb_usuario_tb_tipo_usuario] FOREIGN KEY 
	(
		[tu_id]
	) REFERENCES [dbo].[tb_tipo_usuario] (
		[tu_id]
	),
	CONSTRAINT [FK_tb_usuario_tb_unidade] FOREIGN KEY 
	(
		[un_id]
	) REFERENCES [dbo].[tb_unidade] (
		[un_id]
	)
GO

ALTER TABLE [dbo].[tb_assunto] ADD 
	CONSTRAINT [FK_tb_assunto_tb_usuario] FOREIGN KEY 
	(
		[us_id]
	) REFERENCES [dbo].[tb_usuario] (
		[us_id]
	)
GO

ALTER TABLE [dbo].[tb_orgao_externo] ADD 
	CONSTRAINT [FK_tb_orgao_externo_tb_usuario] FOREIGN KEY 
	(
		[us_id]
	) REFERENCES [dbo].[tb_usuario] (
		[us_id]
	)
GO

ALTER TABLE [dbo].[tb_tipo_documento] ADD 
	CONSTRAINT [FK_tb_tipo_documento_tb_usuario] FOREIGN KEY 
	(
		[us_id]
	) REFERENCES [dbo].[tb_usuario] (
		[us_id]
	)
GO

ALTER TABLE [dbo].[tb_documento] ADD 
	CONSTRAINT [FK_Documento_Assunto] FOREIGN KEY 
	(
		[as_id]
	) REFERENCES [dbo].[tb_assunto] (
		[as_id]
	),
	CONSTRAINT [FK_Documento_OrgaoExterno] FOREIGN KEY 
	(
		[oe_id]
	) REFERENCES [dbo].[tb_orgao_externo] (
		[oe_id]
	),
	CONSTRAINT [FK_Documento_TipoDocumento] FOREIGN KEY 
	(
		[td_id]
	) REFERENCES [dbo].[tb_tipo_documento] (
		[td_id]
	),
	CONSTRAINT [FK_tb_documento_tb_usuario] FOREIGN KEY 
	(
		[us_id]
	) REFERENCES [dbo].[tb_usuario] (
		[us_id]
	)
GO

ALTER TABLE [dbo].[tb_tramitacao] ADD 
	CONSTRAINT [FK_tb_tramitacao_tb_documento] FOREIGN KEY 
	(
		[dc_id]
	) REFERENCES [dbo].[tb_documento] (
		[dc_id]
	),
	CONSTRAINT [FK_tb_tramitacao_tb_unidade] FOREIGN KEY 
	(
		[un_id]
	) REFERENCES [dbo].[tb_unidade] (
		[un_id]
	),
	CONSTRAINT [FK_tb_tramitacao_tb_usuario] FOREIGN KEY 
	(
		[us_id]
	) REFERENCES [dbo].[tb_usuario] (
		[us_id]
	)
GO

