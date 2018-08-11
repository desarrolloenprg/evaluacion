ALTER TABLE ESCUELA ADD CONSTRAINT FK_PAIS_ID FOREIGN KEY(FK_PAIS_ID) REFERENCES PAIS(ID);

ALTER TABLE CURSO ADD CONSTRAINT FK_ESCUELA_ID FOREIGN KEY(FK_ESCUELA_ID) REFERENCES ESCUELA(ID);

ALTER TABLE ETAPA ADD CONSTRAINT FK_CURSO_ID_1 FOREIGN KEY(FK_CURSO_ID) REFERENCES CURSO(ID);

ALTER TABLE RETO ADD CONSTRAINT FK_ETAPA_ID FOREIGN KEY(FK_ETAPA_ID) REFERENCES ETAPA(ID);

ALTER TABLE OBJETIVO_CODE ADD CONSTRAINT FK_RETO_ID FOREIGN KEY(FK_RETO_ID) REFERENCES RETO(ID);
ALTER TABLE OBJETIVO_CODE ADD CONSTRAINT FK_SESION_ID FOREIGN KEY(FK_SESION_ID) REFERENCES SESION(ID);

ALTER TABLE SECCION ADD CONSTRAINT FK_CURSO_ID_2 FOREIGN KEY(FK_CURSO_ID) REFERENCES CURSO(ID);
ALTER TABLE SECCION ADD CONSTRAINT FK_DOCENTE_ID FOREIGN KEY(FK_DOCENTE_ID) REFERENCES DOCENTE(ID);

ALTER TABLE ENTRENADOR ADD CONSTRAINT FK_SECCION_ID FOREIGN KEY(FK_SECCION_ID) REFERENCES SECCION(ID);

ALTER TABLE EQUIPO ADD CONSTRAINT FK_ALUMNO_0_ID FOREIGN KEY(FK_ALUMNO_0_ID) REFERENCES ALUMNO(ID);
ALTER TABLE EQUIPO ADD CONSTRAINT FK_ALUMNO_1_ID FOREIGN KEY(FK_ALUMNO_1_ID) REFERENCES ALUMNO(ID);

ALTER TABLE RUBRICA ADD CONSTRAINT FK_PROYECTO_ID FOREIGN KEY(FK_PROYECTO_ID) REFERENCES PROYECTO(ID);

ALTER TABLE OBJETIVO_RUBRICA ADD CONSTRAINT FK_SESION_ID_1 FOREIGN KEY(FK_SESION_ID) REFERENCES SESION(ID);
ALTER TABLE OBJETIVO_RUBRICA ADD CONSTRAINT FK_RUBRICA_ID FOREIGN KEY(FK_RUBRICA_ID) REFERENCES RUBRICA(ID);
ALTER TABLE OBJETIVO_RUBRICA ADD CONSTRAINT FK_ALUMNO_ID_4 FOREIGN KEY(FK_ALUMNO_ID) REFERENCES ALUMNO(ID);

ALTER TABLE AVANCE_RUBRICA ADD CONSTRAINT FK_ALUMNO_ID FOREIGN KEY(FK_ALUMNO_ID) REFERENCES ALUMNO(ID);

ALTER TABLE AVANCE_CODE ADD CONSTRAINT FK_SESION_ID_2 FOREIGN KEY(FK_SESION_ID) REFERENCES SESION(ID);
ALTER TABLE AVANCE_CODE ADD CONSTRAINT FK_ALUMNO_ID_1 FOREIGN KEY(FK_ALUMNO_ID) REFERENCES ALUMNO(ID);

ALTER TABLE OBJETIVO_VIDEO ADD CONSTRAINT FK_VIDEO_ID FOREIGN KEY(FK_VIDEO_ID) REFERENCES VIDEO(ID);
ALTER TABLE OBJETIVO_VIDEO ADD CONSTRAINT FK_SESION_ID_3 FOREIGN KEY(FK_SESION_ID) REFERENCES SESION(ID);

ALTER TABLE AVANCE_VIDEO ADD CONSTRAINT FK_VIDEO_ID_1 FOREIGN KEY(FK_VIDEO_ID) REFERENCES VIDEO(ID);
ALTER TABLE AVANCE_VIDEO ADD CONSTRAINT FK_ALUMNO_ID_2 FOREIGN KEY(FK_ALUMNO_ID) REFERENCES ALUMNO(ID);

ALTER TABLE PREGUNTA ADD CONSTRAINT FK_SESION_ID_4 FOREIGN KEY(FK_SESION_ID) REFERENCES SESION(ID);

ALTER TABLE AVANCE_PREGUNTA ADD CONSTRAINT FK_PREGUNTA_ID_1 FOREIGN KEY(FK_PREGUNTA_ID) REFERENCES PREGUNTA(ID);
ALTER TABLE AVANCE_PREGUNTA ADD CONSTRAINT FK_ALUMNO_ID_3 FOREIGN KEY(FK_ALUMNO_ID) REFERENCES ALUMNO(ID);