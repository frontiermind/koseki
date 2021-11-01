
-- フォーム情報テーブル
CREATE TABLE forms (
    -- 管理データ
    id                          INT4 UNSIGNED NOT NULL,                 -- ID
    type                        INT2 UNSIGNED NOT NULL,                 -- フォームタイプ (タイプマスタ参照)
    
    -- 送信フォーム設定
    title                       TEXT NOT NULL,                          -- ウィンドウタイトル
    start_stamp                 TIMESTAMP,                              -- 公開開始日時
    end_stamp                   TIMESTAMP,                              -- 公開終了日時
    name                        TEXT NOT NULL,                          -- 送信フォーム名称
    name_size                   INT4 UNSIGNED NOT NULL,                 -- 送信フォーム名称文字サイズ
    name_color                  TEXT NOT NULL,                          -- 送信フォーム名称文字色
    name_img                    INT2 UNSIGNED NOT NULL,                 -- 送信フォーム名称画像の有無 (0 = none / 1 = gif / 2 = png / 3 = jpg)
    comment                     TEXT NOT NULL,                          -- 送信フォーム名称下コメント
    comment_size                INT4 UNSIGNED NOT NULL,                 -- 送信フォーム名称下コメント文字サイズ
    comment_color               TEXT NOT NULL,                          -- 送信フォーム名称下コメント文字色
    comment_position            CHAR(6),                                -- 送信フォーム名称下コメント文位置 (left / center / light)
    submit                      INT2 UNSIGNED NOT NULL,                 -- 送信ボタン表示 (1 = text / 2 = image)
    submit_img                  INT2 UNSIGNED NOT NULL,                 -- 送信ボタン画像の有無 (0 = none / 1 = gif / 2 = png / 3 = jpg)
    submit_caption              TEXT NOT NULL,                          -- 送信ボタンテキスト文字
    return                      INT2 UNSIGNED NOT NULL,                 -- 戻るボタン表示 (0 = none / 1 = text / 2 = image)
    return_caption              TEXT NOT NULL,                          -- 戻るボタンテキスト文字
    return_img                  INT2 UNSIGNED NOT NULL,                 -- 戻るボタン画像の有無 (0 = none / 1 = gif / 2 = png / 3 = jpg)
    return_url                  TEXT NOT NULL,                          -- 戻るボタンURL
    send_mail                   INT2 UNSIGNED NOT NULL,                 -- メール送信 (0 = none / 1 = 回答者 / 2 = 管理者 / 3 = 双方)
    mail_subject                TEXT NOT NULL,                          -- 回答者向けメール件名
    mail_body                   TEXT NOT NULL,                          -- 回答者向けメール本文
    mail_from_name              TEXT NOT NULL,                          -- 回答者向けメール差出人名
    mail_from_address           TEXT NOT NULL,                          -- 回答者向けメール差出人メールアドレス
    confirm                     INT2 UNSIGNED NOT NULL,                 -- 確認画面表示 (bool)

    
    -- 全体デザイン
    body_color                  TEXT NOT NULL,                          -- 全体標準文字色
    body_fontsize               INT4 UNSIGNED NOT NULL,                 -- 全体標準文字サイズ
    body_bgcolor                TEXT NOT NULL,                          -- 全体背景色
    body_background             TEXT NOT NULL,                          -- 全体背景画像の有無 (0 = none / 1 = gif / 2 = png / 3 = jpg)
    body_background_fixed       INT2 UNSIGNED NOT NULL,                 -- 全体背景画像の固定 (bool)
    query_no                    INT2 UNSIGNED NOT NULL,                 -- 質問番号表示 (bool)
    query_no_size               INT4 UNSIGNED NOT NULL,                 -- 質問番号文字サイズ
    query_no_color              TEXT NOT NULL,                          -- 質問番号文字色
    query_no_bgcolor            TEXT NOT NULL,                          -- 質問番号背景色
    query_no_img                INT2 UNSIGNED NOT NULL,                 -- 質問番号背景画像の有無 (0 = none / 1 = gif / 2 = png / 3 = jpg)
    query_no_pretext            TEXT NOT NULL,                          -- 質問番号接頭語
    query_no_pretext_size       INT4 UNSIGNED NOT NULL,                 -- 質問番号接頭語文字サイズ
    query_no_pretext_color      TEXT NOT NULL,                          -- 質問番号接頭語文字色
    query_size                  INT4 UNSIGNED NOT NULL,                 -- 質問部文字サイズ
    query_color                 TEXT NOT NULL,                          -- 質問部文字色
    query_bgcolor               TEXT NOT NULL,                          -- 質問部背景色
    
    input_size                  INT4 UNSIGNED NOT NULL,                 -- 項目部文字サイズ
    input_color                 TEXT NOT NULL,                          -- 項目部文字色
    input_bgcolor               TEXT NOT NULL,                          -- 項目部背景色
    
    precomment_size             INT4 UNSIGNED NOT NULL,                 -- 項目前コメント文字サイズ
    precomment_color            TEXT NOT NULL,                          -- 項目前コメント文字色
    appcomment_size             INT4 UNSIGNED NOT NULL,                 -- 項目後コメント文字サイズ
    appcomment_color            TEXT NOT NULL,                          -- 項目後コメント文字色
    
    PRIMARY KEY(id)
);


-- 質問項目
CREATE TABLE queries (
    id                          INT4 UNSIGNED NOT NULL,                 -- ID
    no                          INT4 UNSIGNED NOT NULL,                 -- 質問番号
    qtype                       INT2 UNSIGNED NOT NULL,                 -- 質問形態 (質問形態マスタ参照)
    answer_list                 TEXT NOT NULL,                          -- 回答内容 (リスト系項目で使用・LF区切り)
    position                    CHAR(6),                                 -- 表示位置 (left / center / light)
    query                       TEXT NOT NULL,                          -- 質問文
    min_limit                   INT4 UNSIGNED NOT NULL,                 -- 選択項目最小個数
    max_limit                   INT4 UNSIGNED NOT NULL,                 -- 選択項目最大個数
    selected                    TEXT NOT NULL,                          -- 選択項目デフォルト値
    input_cols                  INT4 UNSIGNED NOT NULL,                 -- テキスト項目幅
    input_rows                  INT4 UNSIGNED NOT NULL,                 -- テキスト項目行数
    input_type                  INT4 UNSIGNED NOT NULL,                 -- テキスト項目入力種類(制限) (制限種類マスタ参照)
    input_regex                 TEXT NOT NULL,                          -- テキスト項目入力制限 正規表現
    precomment                  TEXT NOT NULL,                          -- 項目前コメント
    appcomment                  TEXT NOT NULL,                          -- 項目後コメント
    required                    INT2 UNSIGNED NOT NULL,                 -- 回答必須 (bool)
    required_text               TEXT NOT NULL,                          -- 必須表示文
    required_text_color         TEXT NOT NULL,                          -- 必須表示文字色
    required_text_size          INT4 UNSIGNED NOT NULL,                 -- 必須表示文字サイズ
    send_mail_address           INT2 UNSIGNED NOT NULL                  -- この内容をToにしてメール送信 (bool)
);


