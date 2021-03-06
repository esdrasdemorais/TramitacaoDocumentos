/****** Object:  Database Documentos    Script Date: 20/10/2009 10:25:03 ******/
IF EXISTS (SELECT name FROM master.dbo.sysdatabases WHERE name = N'Documentos')
	DROP DATABASE [Documentos]
GO

CREATE DATABASE [Documentos]  ON (NAME = N'Documentos_Data', FILENAME = N'e:\mssql\MSSQL\data\Documentos_Data.MDF' , SIZE = 1, FILEGROWTH = 10%) LOG ON (NAME = N'Documentos_Log', FILENAME = N'e:\mssql\MSSQL\data\Documentos_Log.LDF' , SIZE = 1, FILEGROWTH = 10%)
GO

exec sp_dboption N'Documentos', N'autoclose', N'true'
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

if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[FK_Documento_Assunto]') and OBJECTPROPERTY(id, N'IsForeignKey') = 1)
ALTER TABLE [dbo].[Documento] DROP CONSTRAINT FK_Documento_Assunto
GO

if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[FK_Documento_OrgaoExterno]') and OBJECTPROPERTY(id, N'IsForeignKey') = 1)
ALTER TABLE [dbo].[Documento] DROP CONSTRAINT FK_Documento_OrgaoExterno
GO

if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[FK_Documento_TipoDocumento]') and OBJECTPROPERTY(id, N'IsForeignKey') = 1)
ALTER TABLE [dbo].[Documento] DROP CONSTRAINT FK_Documento_TipoDocumento
GO

if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[FK_Tramitacao_Documento]') and OBJECTPROPERTY(id, N'IsForeignKey') = 1)
ALTER TABLE [dbo].[Tramitacao] DROP CONSTRAINT FK_Tramitacao_Documento
GO

/****** Object:  Table [dbo].[Tramitacao]    Script Date: 20/10/2009 10:25:04 ******/
if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[Tramitacao]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)
drop table [dbo].[Tramitacao]
GO

/****** Object:  Table [dbo].[Documento]    Script Date: 20/10/2009 10:25:04 ******/
if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[Documento]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)
drop table [dbo].[Documento]
GO

/****** Object:  Table [dbo].[Assunto]    Script Date: 20/10/2009 10:25:04 ******/
if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[Assunto]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)
drop table [dbo].[Assunto]
GO

/****** Object:  Table [dbo].[OrgaoExterno]    Script Date: 20/10/2009 10:25:04 ******/
if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[OrgaoExterno]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)
drop table [dbo].[OrgaoExterno]
GO

/****** Object:  Table [dbo].[TipoDocumento]    Script Date: 20/10/2009 10:25:04 ******/
if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[TipoDocumento]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)
drop table [dbo].[TipoDocumento]
GO

/****** Object:  Table [dbo].[TipoUsuario]    Script Date: 20/10/2009 10:25:04 ******/
if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[TipoUsuario]') and OBJECTPROPERTY(id, N'IsUserTable') = 1)
drop table [dbo].[TipoUsuario]
GO

/****** Object:  Login distributor_admin    Script Date: 20/10/2009 10:25:03 ******/
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

/****** Object:  Login BUILTIN\Administradores    Script Date: 20/10/2009 10:25:03 ******/
exec sp_addsrvrolemember N'BUILTIN\Administradores', sysadmin
GO

/****** Object:  Login distributor_admin    Script Date: 20/10/2009 10:25:03 ******/
exec sp_addsrvrolemember N'distributor_admin', sysadmin
GO

/****** Object:  Table [dbo].[Assunto]    Script Date: 20/10/2009 10:25:04 ******/
CREATE TABLE [dbo].[Assunto] (
	[id_assunto] [int] IDENTITY (1, 1) NOT NULL ,
	[desc_assunto] [varchar] (150) NOT NULL 
) ON [PRIMARY]
GO

/****** Object:  Table [dbo].[OrgaoExterno]    Script Date: 20/10/2009 10:25:05 ******/
CREATE TABLE [dbo].[OrgaoExterno] (
	[id_orgao_externo] [int] IDENTITY (1, 1) NOT NULL ,
	[desc_orgao_externo] [varchar] (150) NOT NULL 
) ON [PRIMARY]
GO

/****** Object:  Table [dbo].[TipoDocumento]    Script Date: 20/10/2009 10:25:05 ******/
CREATE TABLE [dbo].[TipoDocumento] (
	[id_tipo_documento] [int] IDENTITY (1, 1) NOT NULL ,
	[desc_tipo_documento] [varchar] (150) NOT NULL 
) ON [PRIMARY]
GO

/****** Object:  Table [dbo].[TipoUsuario]    Script Date: 20/10/2009 10:25:05 ******/
CREATE TABLE [dbo].[TipoUsuario] (
	[id_tipo] [int] IDENTITY (1, 1) NOT NULL ,
	[desc_tipo] [varchar] (50) NOT NULL 
) ON [PRIMARY]
GO

/****** Object:  Table [dbo].[Documento]    Script Date: 20/10/2009 10:25:05 ******/
CREATE TABLE [dbo].[Documento] (
	[id_documento] [int] IDENTITY (1, 1) NOT NULL ,
	[id_tipo_documento] [int] NOT NULL ,
	[id_usuario] [int] NOT NULL ,
	[data_elaboracao] [datetime] NOT NULL ,
	[id_assunto] [int] NOT NULL ,
	[compl_assunto] [varchar] (150) NULL ,
	[id_orgao_externo] [int] NULL 
) ON [PRIMARY]
GO

/****** Object:  Table [dbo].[Tramitacao]    Script Date: 20/10/2009 10:25:05 ******/
CREATE TABLE [dbo].[Tramitacao] (
	[id_tramitacao] [int] IDENTITY (1, 1) NOT NULL ,
	[id_documento] [int] NOT NULL ,
	[id_tipo_documento] [int] NOT NULL ,
	[data_tramitacao] [datetime] NOT NULL ,
	[cota_tramitacao] [varchar] (200) NULL ,
	[id_usuario_origem] [int] NOT NULL ,
	[id_usuario_destino] [int] NOT NULL 
) ON [PRIMARY]
GO

ALTER TABLE [dbo].[Assunto] WITH NOCHECK ADD 
	CONSTRAINT [PK_Assunto] PRIMARY KEY  CLUSTERED 
	(
		[id_assunto]
	)  ON [PRIMARY] 
GO

ALTER TABLE [dbo].[OrgaoExterno] WITH NOCHECK ADD 
	CONSTRAINT [PK_OrgaoExterno] PRIMARY KEY  CLUSTERED 
	(
		[id_orgao_externo]
	)  ON [PRIMARY] 
GO

ALTER TABLE [dbo].[TipoDocumento] WITH NOCHECK ADD 
	CONSTRAINT [PK_TipoDocumento] PRIMARY KEY  CLUSTERED 
	(
		[id_tipo_documento]
	)  ON [PRIMARY] 
GO

ALTER TABLE [dbo].[TipoUsuario] WITH NOCHECK ADD 
	CONSTRAINT [PK_TipoUsuario] PRIMARY KEY  CLUSTERED 
	(
		[id_tipo]
	)  ON [PRIMARY] 
GO

ALTER TABLE [dbo].[Documento] WITH NOCHECK ADD 
	CONSTRAINT [PK_Documento] PRIMARY KEY  CLUSTERED 
	(
		[id_documento],
		[id_tipo_documento]
	)  ON [PRIMARY] 
GO

ALTER TABLE [dbo].[Tramitacao] WITH NOCHECK ADD 
	CONSTRAINT [PK_Tramitacao] PRIMARY KEY  CLUSTERED 
	(
		[id_tramitacao]
	)  ON [PRIMARY] 
GO

ALTER TABLE [dbo].[Documento] ADD 
	CONSTRAINT [FK_Documento_Assunto] FOREIGN KEY 
	(
		[id_assunto]
	) REFERENCES [dbo].[Assunto] (
		[id_assunto]
	),
	CONSTRAINT [FK_Documento_OrgaoExterno] FOREIGN KEY 
	(
		[id_orgao_externo]
	) REFERENCES [dbo].[OrgaoExterno] (
		[id_orgao_externo]
	),
	CONSTRAINT [FK_Documento_TipoDocumento] FOREIGN KEY 
	(
		[id_tipo_documento]
	) REFERENCES [dbo].[TipoDocumento] (
		[id_tipo_documento]
	)
GO

ALTER TABLE [dbo].[Tramitacao] ADD 
	CONSTRAINT [FK_Tramitacao_Documento] FOREIGN KEY 
	(
		[id_documento],
		[id_tipo_documento]
	) REFERENCES [dbo].[Documento] (
		[id_documento],
		[id_tipo_documento]
	)
GO

/*
Results:
Deleting database file 'e:\mssql\MSSQL\data\Documentos_Log.LDF'.
Deleting database file 'e:\mssql\MSSQL\data\Documentos_Data.MDF'.
The CREATE DATABASE process is allocating 1.00 MB on disk 'Documentos_Data'.
The CREATE DATABASE process is allocating 1.00 MB on disk 'Documentos_Log'.
'BUILTIN\Administradores' added to role 'sysadmin'.
'distributor_admin' added to role 'sysadmin'.
*/