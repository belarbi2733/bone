INSERT INTO `prix_service` (`Id_service`, `Id_type_service`, `Prix`) VALUES
(1, 1, 2),
(2, 1, 2),
(3, 1, 0),
(1, 2, 4),
(2, 2, 4),
(3, 2, 0),
(1, 3, 6),
(2, 3, 6),
(3, 3, 2);

INSERT INTO `service` (`Id_service`, `Label`) VALUES
(1, 'CAD\r\nFOR\r\nOSTEOPOROSIS'),
(2, 'CAD\r\nFOR\r\nSCOLIOSIS'),
(3, 'MEDICAL\r\nALGORITHMS\r\nTOOLBOX');


INSERT INTO `type_service` (`Id_Type_service`, `Label`) VALUES
(1, 'Basic'),
(2, 'Advanced'),
(3, 'Personalized');