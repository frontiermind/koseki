
-- �ե��������ơ��֥�
CREATE TABLE forms (
    -- �����ǡ���
    id                          INT4 UNSIGNED NOT NULL,                 -- ID
    type                        INT2 UNSIGNED NOT NULL,                 -- �ե����ॿ���� (�����ץޥ�������)
    
    -- �����ե���������
    title                       TEXT NOT NULL,                          -- ������ɥ������ȥ�
    start_stamp                 TIMESTAMP,                              -- ������������
    end_stamp                   TIMESTAMP,                              -- ������λ����
    name                        TEXT NOT NULL,                          -- �����ե�����̾��
    name_size                   INT4 UNSIGNED NOT NULL,                 -- �����ե�����̾��ʸ��������
    name_color                  TEXT NOT NULL,                          -- �����ե�����̾��ʸ����
    name_img                    INT2 UNSIGNED NOT NULL,                 -- �����ե�����̾�β�����̵ͭ (0 = none / 1 = gif / 2 = png / 3 = jpg)
    comment                     TEXT NOT NULL,                          -- �����ե�����̾�β�������
    comment_size                INT4 UNSIGNED NOT NULL,                 -- �����ե�����̾�β�������ʸ��������
    comment_color               TEXT NOT NULL,                          -- �����ե�����̾�β�������ʸ����
    comment_position            CHAR(6),                                -- �����ե�����̾�β�������ʸ���� (left / center / light)
    submit                      INT2 UNSIGNED NOT NULL,                 -- �����ܥ���ɽ�� (1 = text / 2 = image)
    submit_img                  INT2 UNSIGNED NOT NULL,                 -- �����ܥ��������̵ͭ (0 = none / 1 = gif / 2 = png / 3 = jpg)
    submit_caption              TEXT NOT NULL,                          -- �����ܥ���ƥ�����ʸ��
    return                      INT2 UNSIGNED NOT NULL,                 -- ���ܥ���ɽ�� (0 = none / 1 = text / 2 = image)
    return_caption              TEXT NOT NULL,                          -- ���ܥ���ƥ�����ʸ��
    return_img                  INT2 UNSIGNED NOT NULL,                 -- ���ܥ��������̵ͭ (0 = none / 1 = gif / 2 = png / 3 = jpg)
    return_url                  TEXT NOT NULL,                          -- ���ܥ���URL
    send_mail                   INT2 UNSIGNED NOT NULL,                 -- �᡼������ (0 = none / 1 = ������ / 2 = ������ / 3 = ����)
    mail_subject                TEXT NOT NULL,                          -- �����Ը����᡼���̾
    mail_body                   TEXT NOT NULL,                          -- �����Ը����᡼����ʸ
    mail_from_name              TEXT NOT NULL,                          -- �����Ը����᡼�뺹�п�̾
    mail_from_address           TEXT NOT NULL,                          -- �����Ը����᡼�뺹�пͥ᡼�륢�ɥ쥹
    confirm                     INT2 UNSIGNED NOT NULL,                 -- ��ǧ����ɽ�� (bool)

    
    -- ���Υǥ�����
    body_color                  TEXT NOT NULL,                          -- ����ɸ��ʸ����
    body_fontsize               INT4 UNSIGNED NOT NULL,                 -- ����ɸ��ʸ��������
    body_bgcolor                TEXT NOT NULL,                          -- �����طʿ�
    body_background             TEXT NOT NULL,                          -- �����طʲ�����̵ͭ (0 = none / 1 = gif / 2 = png / 3 = jpg)
    body_background_fixed       INT2 UNSIGNED NOT NULL,                 -- �����طʲ����θ��� (bool)
    query_no                    INT2 UNSIGNED NOT NULL,                 -- �����ֹ�ɽ�� (bool)
    query_no_size               INT4 UNSIGNED NOT NULL,                 -- �����ֹ�ʸ��������
    query_no_color              TEXT NOT NULL,                          -- �����ֹ�ʸ����
    query_no_bgcolor            TEXT NOT NULL,                          -- �����ֹ��طʿ�
    query_no_img                INT2 UNSIGNED NOT NULL,                 -- �����ֹ��طʲ�����̵ͭ (0 = none / 1 = gif / 2 = png / 3 = jpg)
    query_no_pretext            TEXT NOT NULL,                          -- �����ֹ���Ƭ��
    query_no_pretext_size       INT4 UNSIGNED NOT NULL,                 -- �����ֹ���Ƭ��ʸ��������
    query_no_pretext_color      TEXT NOT NULL,                          -- �����ֹ���Ƭ��ʸ����
    query_size                  INT4 UNSIGNED NOT NULL,                 -- ������ʸ��������
    query_color                 TEXT NOT NULL,                          -- ������ʸ����
    query_bgcolor               TEXT NOT NULL,                          -- �������طʿ�
    
    input_size                  INT4 UNSIGNED NOT NULL,                 -- ������ʸ��������
    input_color                 TEXT NOT NULL,                          -- ������ʸ����
    input_bgcolor               TEXT NOT NULL,                          -- �������طʿ�
    
    precomment_size             INT4 UNSIGNED NOT NULL,                 -- ������������ʸ��������
    precomment_color            TEXT NOT NULL,                          -- ������������ʸ����
    appcomment_size             INT4 UNSIGNED NOT NULL,                 -- ���ܸ女����ʸ��������
    appcomment_color            TEXT NOT NULL,                          -- ���ܸ女����ʸ����
    
    PRIMARY KEY(id)
);


-- �������
CREATE TABLE queries (
    id                          INT4 UNSIGNED NOT NULL,                 -- ID
    no                          INT4 UNSIGNED NOT NULL,                 -- �����ֹ�
    qtype                       INT2 UNSIGNED NOT NULL,                 -- ������� (������֥ޥ�������)
    answer_list                 TEXT NOT NULL,                          -- �������� (�ꥹ�ȷϹ��ܤǻ��ѡ�LF���ڤ�)
    position                    CHAR(6),                                 -- ɽ������ (left / center / light)
    query                       TEXT NOT NULL,                          -- ����ʸ
    min_limit                   INT4 UNSIGNED NOT NULL,                 -- ������ܺǾ��Ŀ�
    max_limit                   INT4 UNSIGNED NOT NULL,                 -- ������ܺ���Ŀ�
    selected                    TEXT NOT NULL,                          -- ������ܥǥե������
    input_cols                  INT4 UNSIGNED NOT NULL,                 -- �ƥ����ȹ�����
    input_rows                  INT4 UNSIGNED NOT NULL,                 -- �ƥ����ȹ��ܹԿ�
    input_type                  INT4 UNSIGNED NOT NULL,                 -- �ƥ����ȹ������ϼ���(����) (���¼���ޥ�������)
    input_regex                 TEXT NOT NULL,                          -- �ƥ����ȹ����������� ����ɽ��
    precomment                  TEXT NOT NULL,                          -- ������������
    appcomment                  TEXT NOT NULL,                          -- ���ܸ女����
    required                    INT2 UNSIGNED NOT NULL,                 -- ����ɬ�� (bool)
    required_text               TEXT NOT NULL,                          -- ɬ��ɽ��ʸ
    required_text_color         TEXT NOT NULL,                          -- ɬ��ɽ��ʸ����
    required_text_size          INT4 UNSIGNED NOT NULL,                 -- ɬ��ɽ��ʸ��������
    send_mail_address           INT2 UNSIGNED NOT NULL                  -- �������Ƥ�To�ˤ��ƥ᡼������ (bool)
);


