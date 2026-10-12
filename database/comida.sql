--
-- PostgreSQL database dump
--

\restrict zh9xvcgOZBvHfDjiSq44Tpv5abq3m0ocdD4FU7Ru245icsYIHDxM91lm2Gnubn3

-- Dumped from database version 17.6
-- Dumped by pg_dump version 17.6

-- Started on 2026-10-11 22:07:53

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 5 (class 2615 OID 105534)
-- Name: public; Type: SCHEMA; Schema: -; Owner: postgres
--

-- *not* creating schema, since initdb creates it


ALTER SCHEMA public OWNER TO postgres;

--
-- TOC entry 5328 (class 0 OID 0)
-- Dependencies: 5
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: postgres
--

COMMENT ON SCHEMA public IS '';


SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 256 (class 1259 OID 105906)
-- Name: bitacora; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.bitacora (
    cod_bitacora bigint NOT NULL,
    cod_usuario_bit bigint,
    acc_bit character varying(60) NOT NULL,
    tab_bit character varying(60),
    reg_bit bigint,
    det_bit jsonb,
    ip_bit character varying(45),
    fec_cre_bit timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.bitacora OWNER TO postgres;

--
-- TOC entry 5330 (class 0 OID 0)
-- Dependencies: 256
-- Name: TABLE bitacora; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.bitacora IS 'Requerimiento 20. Registra accesos, modificaciones, consultas y generacion de recetas.';


--
-- TOC entry 255 (class 1259 OID 105905)
-- Name: bitacora_cod_bitacora_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.bitacora_cod_bitacora_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.bitacora_cod_bitacora_seq OWNER TO postgres;

--
-- TOC entry 5331 (class 0 OID 0)
-- Dependencies: 255
-- Name: bitacora_cod_bitacora_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.bitacora_cod_bitacora_seq OWNED BY public.bitacora.cod_bitacora;


--
-- TOC entry 263 (class 1259 OID 105957)
-- Name: cache; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration integer NOT NULL
);


ALTER TABLE public.cache OWNER TO postgres;

--
-- TOC entry 264 (class 1259 OID 105964)
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration integer NOT NULL
);


ALTER TABLE public.cache_locks OWNER TO postgres;

--
-- TOC entry 227 (class 1259 OID 105595)
-- Name: categorias_ingrediente; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.categorias_ingrediente (
    cod_categoria_ingrediente integer NOT NULL,
    nom_cat character varying(100) NOT NULL,
    des_cat character varying(255),
    ico_cat character varying(300),
    act_cat boolean DEFAULT true NOT NULL,
    fec_cre_cat timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.categorias_ingrediente OWNER TO postgres;

--
-- TOC entry 226 (class 1259 OID 105594)
-- Name: categorias_ingrediente_cod_categoria_ingrediente_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.categorias_ingrediente_cod_categoria_ingrediente_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.categorias_ingrediente_cod_categoria_ingrediente_seq OWNER TO postgres;

--
-- TOC entry 5332 (class 0 OID 0)
-- Dependencies: 226
-- Name: categorias_ingrediente_cod_categoria_ingrediente_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.categorias_ingrediente_cod_categoria_ingrediente_seq OWNED BY public.categorias_ingrediente.cod_categoria_ingrediente;


--
-- TOC entry 233 (class 1259 OID 105642)
-- Name: categorias_receta; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.categorias_receta (
    cod_categoria_receta integer NOT NULL,
    nom_car character varying(100) NOT NULL,
    des_car character varying(255),
    ico_car character varying(300),
    act_car boolean DEFAULT true NOT NULL,
    fec_cre_car timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.categorias_receta OWNER TO postgres;

--
-- TOC entry 232 (class 1259 OID 105641)
-- Name: categorias_receta_cod_categoria_receta_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.categorias_receta_cod_categoria_receta_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.categorias_receta_cod_categoria_receta_seq OWNER TO postgres;

--
-- TOC entry 5333 (class 0 OID 0)
-- Dependencies: 232
-- Name: categorias_receta_cod_categoria_receta_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.categorias_receta_cod_categoria_receta_seq OWNED BY public.categorias_receta.cod_categoria_receta;


--
-- TOC entry 235 (class 1259 OID 105655)
-- Name: etiquetas; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.etiquetas (
    cod_etiqueta integer NOT NULL,
    nom_eti character varying(80) NOT NULL,
    col_eti character varying(7) DEFAULT '#2E74B5'::character varying NOT NULL,
    act_eti boolean DEFAULT true NOT NULL,
    CONSTRAINT etiquetas_color_check CHECK (((col_eti)::text ~ '^#[0-9A-Fa-f]{6}$'::text))
);


ALTER TABLE public.etiquetas OWNER TO postgres;

--
-- TOC entry 234 (class 1259 OID 105654)
-- Name: etiquetas_cod_etiqueta_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.etiquetas_cod_etiqueta_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.etiquetas_cod_etiqueta_seq OWNER TO postgres;

--
-- TOC entry 5334 (class 0 OID 0)
-- Dependencies: 234
-- Name: etiquetas_cod_etiqueta_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.etiquetas_cod_etiqueta_seq OWNED BY public.etiquetas.cod_etiqueta;


--
-- TOC entry 268 (class 1259 OID 105982)
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.failed_jobs OWNER TO postgres;

--
-- TOC entry 267 (class 1259 OID 105981)
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.failed_jobs_id_seq OWNER TO postgres;

--
-- TOC entry 5335 (class 0 OID 0)
-- Dependencies: 267
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- TOC entry 252 (class 1259 OID 105861)
-- Name: favoritos; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.favoritos (
    cod_favorito bigint NOT NULL,
    cod_usuario_fav bigint NOT NULL,
    cod_receta_fav bigint NOT NULL,
    fec_cre_fav timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.favoritos OWNER TO postgres;

--
-- TOC entry 5336 (class 0 OID 0)
-- Dependencies: 252
-- Name: TABLE favoritos; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.favoritos IS 'Historia de Usuario 8. Antes dependia de valoraciones.pre_val, que obligaba a puntuar para guardar.';


--
-- TOC entry 251 (class 1259 OID 105860)
-- Name: favoritos_cod_favorito_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.favoritos_cod_favorito_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.favoritos_cod_favorito_seq OWNER TO postgres;

--
-- TOC entry 5337 (class 0 OID 0)
-- Dependencies: 251
-- Name: favoritos_cod_favorito_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.favoritos_cod_favorito_seq OWNED BY public.favoritos.cod_favorito;


--
-- TOC entry 246 (class 1259 OID 105797)
-- Name: historial_consulta; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.historial_consulta (
    cod_consulta bigint NOT NULL,
    cod_usuario_con bigint NOT NULL,
    ing_json_con jsonb NOT NULL,
    fil_json_con jsonb,
    tex_lib_con text,
    tot_res_con smallint DEFAULT 0 NOT NULL,
    tie_res_con integer,
    fec_cre_con timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.historial_consulta OWNER TO postgres;

--
-- TOC entry 5338 (class 0 OID 0)
-- Dependencies: 246
-- Name: COLUMN historial_consulta.ing_json_con; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.historial_consulta.ing_json_con IS 'Ingredientes enviados en la consulta, en formato JSON.';


--
-- TOC entry 5339 (class 0 OID 0)
-- Dependencies: 246
-- Name: COLUMN historial_consulta.tie_res_con; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.historial_consulta.tie_res_con IS 'Tiempo de respuesta en milisegundos. Sirve para el objetivo especifico de evaluar la eficiencia.';


--
-- TOC entry 245 (class 1259 OID 105796)
-- Name: historial_consulta_cod_consulta_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.historial_consulta_cod_consulta_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.historial_consulta_cod_consulta_seq OWNER TO postgres;

--
-- TOC entry 5340 (class 0 OID 0)
-- Dependencies: 245
-- Name: historial_consulta_cod_consulta_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.historial_consulta_cod_consulta_seq OWNED BY public.historial_consulta.cod_consulta;


--
-- TOC entry 248 (class 1259 OID 105814)
-- Name: historial_resultado; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.historial_resultado (
    cod_resultado bigint NOT NULL,
    cod_consulta_res bigint NOT NULL,
    cod_receta_res bigint,
    pos_res smallint NOT NULL,
    sco_res numeric(5,2) DEFAULT 0 NOT NULL,
    fal_json_res jsonb,
    sel_res boolean DEFAULT false NOT NULL,
    fec_cre_res timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    CONSTRAINT historial_resultado_score_check CHECK (((sco_res >= (0)::numeric) AND (sco_res <= (100)::numeric)))
);


ALTER TABLE public.historial_resultado OWNER TO postgres;

--
-- TOC entry 5341 (class 0 OID 0)
-- Dependencies: 248
-- Name: COLUMN historial_resultado.sco_res; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.historial_resultado.sco_res IS 'Porcentaje de compatibilidad de la receta con el inventario, de 0 a 100.';


--
-- TOC entry 5342 (class 0 OID 0)
-- Dependencies: 248
-- Name: COLUMN historial_resultado.fal_json_res; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.historial_resultado.fal_json_res IS 'Ingredientes faltantes para completar la receta.';


--
-- TOC entry 247 (class 1259 OID 105813)
-- Name: historial_resultado_cod_resultado_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.historial_resultado_cod_resultado_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.historial_resultado_cod_resultado_seq OWNER TO postgres;

--
-- TOC entry 5343 (class 0 OID 0)
-- Dependencies: 247
-- Name: historial_resultado_cod_resultado_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.historial_resultado_cod_resultado_seq OWNED BY public.historial_resultado.cod_resultado;


--
-- TOC entry 231 (class 1259 OID 105622)
-- Name: ingredientes; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.ingredientes (
    cod_ingrediente bigint NOT NULL,
    cod_categoria_ingrediente_ing integer NOT NULL,
    nom_ing character varying(150) NOT NULL,
    sin_ing character varying(300),
    cal_ing numeric(7,2),
    pro_ing numeric(6,2),
    car_ing numeric(6,2),
    gra_ing numeric(6,2),
    ale_ing boolean DEFAULT false NOT NULL,
    img_ing character varying(300),
    act_ing boolean DEFAULT true NOT NULL,
    fec_cre_ing timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    fec_act_ing timestamp(0) without time zone,
    den_ing numeric(7,3),
    pre_ing numeric(8,2),
    CONSTRAINT ingredientes_densidad_check CHECK (((den_ing IS NULL) OR (den_ing > (0)::numeric)))
);


ALTER TABLE public.ingredientes OWNER TO postgres;

--
-- TOC entry 5344 (class 0 OID 0)
-- Dependencies: 231
-- Name: COLUMN ingredientes.sin_ing; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.ingredientes.sin_ing IS 'Sinonimos separados por coma, para la busqueda por nombre alternativo.';


--
-- TOC entry 5345 (class 0 OID 0)
-- Dependencies: 231
-- Name: COLUMN ingredientes.cal_ing; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.ingredientes.cal_ing IS 'Calorias por cada 100 gramos.';


--
-- TOC entry 5346 (class 0 OID 0)
-- Dependencies: 231
-- Name: COLUMN ingredientes.ale_ing; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.ingredientes.ale_ing IS 'Indica si el ingrediente es un alergeno comun.';


--
-- TOC entry 5347 (class 0 OID 0)
-- Dependencies: 231
-- Name: COLUMN ingredientes.den_ing; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.ingredientes.den_ing IS 'Densidad aparente en gramos por mililitro. Permite convertir una medida de volumen (taza, cucharada) a masa. NULL cuando el alimento no se mide por volumen.';


--
-- TOC entry 5348 (class 0 OID 0)
-- Dependencies: 231
-- Name: COLUMN ingredientes.pre_ing; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.ingredientes.pre_ing IS 'Precio de referencia en Bs por kilogramo. Estimacion, no contabilidad.';


--
-- TOC entry 230 (class 1259 OID 105621)
-- Name: ingredientes_cod_ingrediente_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.ingredientes_cod_ingrediente_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.ingredientes_cod_ingrediente_seq OWNER TO postgres;

--
-- TOC entry 5349 (class 0 OID 0)
-- Dependencies: 230
-- Name: ingredientes_cod_ingrediente_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.ingredientes_cod_ingrediente_seq OWNED BY public.ingredientes.cod_ingrediente;


--
-- TOC entry 239 (class 1259 OID 105705)
-- Name: ingredientes_receta; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.ingredientes_receta (
    cod_ingrediente_receta bigint NOT NULL,
    cod_receta_inr bigint NOT NULL,
    cod_ingrediente_inr bigint NOT NULL,
    cod_unidad_inr smallint NOT NULL,
    can_inr numeric(10,3) NOT NULL,
    opc_inr boolean DEFAULT false NOT NULL,
    ord_inr smallint DEFAULT 99 NOT NULL,
    not_inr character varying(200),
    CONSTRAINT ingredientes_receta_cantidad_check CHECK ((can_inr > (0)::numeric))
);


ALTER TABLE public.ingredientes_receta OWNER TO postgres;

--
-- TOC entry 5350 (class 0 OID 0)
-- Dependencies: 239
-- Name: COLUMN ingredientes_receta.opc_inr; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.ingredientes_receta.opc_inr IS 'Ingrediente opcional: no cuenta para el porcentaje de compatibilidad.';


--
-- TOC entry 238 (class 1259 OID 105704)
-- Name: ingredientes_receta_cod_ingrediente_receta_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.ingredientes_receta_cod_ingrediente_receta_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.ingredientes_receta_cod_ingrediente_receta_seq OWNER TO postgres;

--
-- TOC entry 5351 (class 0 OID 0)
-- Dependencies: 238
-- Name: ingredientes_receta_cod_ingrediente_receta_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.ingredientes_receta_cod_ingrediente_receta_seq OWNED BY public.ingredientes_receta.cod_ingrediente_receta;


--
-- TOC entry 244 (class 1259 OID 105767)
-- Name: inventario; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.inventario (
    cod_inventario bigint NOT NULL,
    cod_usuario_inv bigint NOT NULL,
    cod_ingrediente_inv bigint NOT NULL,
    cod_unidad_inv smallint NOT NULL,
    can_inv numeric(10,3) DEFAULT 1 NOT NULL,
    fec_cad_inv date,
    not_inv character varying(300),
    act_inv boolean DEFAULT true NOT NULL,
    fec_cre_inv timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    fec_act_inv timestamp(0) without time zone,
    CONSTRAINT inventario_cantidad_check CHECK ((can_inv >= (0)::numeric))
);


ALTER TABLE public.inventario OWNER TO postgres;

--
-- TOC entry 5352 (class 0 OID 0)
-- Dependencies: 244
-- Name: TABLE inventario; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.inventario IS 'Alimentos que cada usuario tiene disponibles en su hogar.';


--
-- TOC entry 243 (class 1259 OID 105766)
-- Name: inventario_cod_inventario_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.inventario_cod_inventario_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.inventario_cod_inventario_seq OWNER TO postgres;

--
-- TOC entry 5353 (class 0 OID 0)
-- Dependencies: 243
-- Name: inventario_cod_inventario_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.inventario_cod_inventario_seq OWNED BY public.inventario.cod_inventario;


--
-- TOC entry 266 (class 1259 OID 105972)
-- Name: jobs; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.jobs (
    id bigint NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    attempts smallint NOT NULL,
    reserved_at integer,
    available_at integer NOT NULL,
    created_at integer NOT NULL
);


ALTER TABLE public.jobs OWNER TO postgres;

--
-- TOC entry 265 (class 1259 OID 105971)
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.jobs_id_seq OWNER TO postgres;

--
-- TOC entry 5354 (class 0 OID 0)
-- Dependencies: 265
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.jobs_id_seq OWNED BY public.jobs.id;


--
-- TOC entry 258 (class 1259 OID 105923)
-- Name: migrations; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


ALTER TABLE public.migrations OWNER TO postgres;

--
-- TOC entry 257 (class 1259 OID 105922)
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.migrations_id_seq OWNER TO postgres;

--
-- TOC entry 5355 (class 0 OID 0)
-- Dependencies: 257
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- TOC entry 241 (class 1259 OID 105733)
-- Name: pasos_receta; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.pasos_receta (
    cod_paso bigint NOT NULL,
    cod_receta_pas bigint NOT NULL,
    num_pas smallint NOT NULL,
    ins_pas text NOT NULL,
    tie_pas smallint,
    img_pas character varying(500),
    cri_pas boolean DEFAULT false NOT NULL,
    CONSTRAINT pasos_receta_numero_check CHECK ((num_pas > 0))
);


ALTER TABLE public.pasos_receta OWNER TO postgres;

--
-- TOC entry 5356 (class 0 OID 0)
-- Dependencies: 241
-- Name: COLUMN pasos_receta.cri_pas; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.pasos_receta.cri_pas IS 'Paso critico que el usuario no debe omitir.';


--
-- TOC entry 240 (class 1259 OID 105732)
-- Name: pasos_receta_cod_paso_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.pasos_receta_cod_paso_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.pasos_receta_cod_paso_seq OWNER TO postgres;

--
-- TOC entry 5357 (class 0 OID 0)
-- Dependencies: 240
-- Name: pasos_receta_cod_paso_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.pasos_receta_cod_paso_seq OWNED BY public.pasos_receta.cod_paso;


--
-- TOC entry 260 (class 1259 OID 105938)
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


ALTER TABLE public.password_reset_tokens OWNER TO postgres;

--
-- TOC entry 262 (class 1259 OID 105946)
-- Name: personal_access_tokens; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.personal_access_tokens (
    id bigint NOT NULL,
    tokenable_type character varying(255) NOT NULL,
    tokenable_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    token character varying(64) NOT NULL,
    abilities text,
    last_used_at timestamp(0) without time zone,
    expires_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.personal_access_tokens OWNER TO postgres;

--
-- TOC entry 261 (class 1259 OID 105945)
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.personal_access_tokens_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.personal_access_tokens_id_seq OWNER TO postgres;

--
-- TOC entry 5358 (class 0 OID 0)
-- Dependencies: 261
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.personal_access_tokens_id_seq OWNED BY public.personal_access_tokens.id;


--
-- TOC entry 222 (class 1259 OID 105547)
-- Name: preferencias_dieta; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.preferencias_dieta (
    cod_preferencia smallint NOT NULL,
    nom_pre character varying(80) NOT NULL,
    des_pre character varying(255),
    act_pre boolean DEFAULT true NOT NULL,
    fec_cre_pre timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.preferencias_dieta OWNER TO postgres;

--
-- TOC entry 5359 (class 0 OID 0)
-- Dependencies: 222
-- Name: TABLE preferencias_dieta; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.preferencias_dieta IS 'Catalogo de preferencias alimentarias (vegetariano, sin gluten, etc.).';


--
-- TOC entry 221 (class 1259 OID 105546)
-- Name: preferencias_dieta_cod_preferencia_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.preferencias_dieta_cod_preferencia_seq
    AS smallint
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.preferencias_dieta_cod_preferencia_seq OWNER TO postgres;

--
-- TOC entry 5360 (class 0 OID 0)
-- Dependencies: 221
-- Name: preferencias_dieta_cod_preferencia_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.preferencias_dieta_cod_preferencia_seq OWNED BY public.preferencias_dieta.cod_preferencia;


--
-- TOC entry 242 (class 1259 OID 105750)
-- Name: receta_etiquetas; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.receta_etiquetas (
    cod_receta_ret bigint NOT NULL,
    cod_etiqueta_ret integer NOT NULL,
    fec_cre_ret timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.receta_etiquetas OWNER TO postgres;

--
-- TOC entry 237 (class 1259 OID 105667)
-- Name: recetas; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.recetas (
    cod_receta bigint NOT NULL,
    cod_categoria_receta_rec integer NOT NULL,
    cod_usuario_rec bigint,
    nom_rec character varying(200) NOT NULL,
    des_rec text,
    tie_pre_rec smallint DEFAULT 10 NOT NULL,
    tie_coc_rec smallint DEFAULT 0 NOT NULL,
    tie_tot_rec smallint GENERATED ALWAYS AS ((tie_pre_rec + tie_coc_rec)) STORED,
    por_rec smallint DEFAULT 2 NOT NULL,
    niv_rec character varying(10) DEFAULT 'medio'::character varying NOT NULL,
    ori_rec character varying(15) DEFAULT 'sistema'::character varying NOT NULL,
    img_rec character varying(500),
    cal_por_rec numeric(7,2),
    pro_por_rec numeric(6,2),
    car_por_rec numeric(6,2),
    gra_por_rec numeric(6,2),
    obs_nut_rec text,
    est_val_rec character varying(15) DEFAULT 'pendiente'::character varying NOT NULL,
    ver_rec boolean DEFAULT false NOT NULL,
    act_rec boolean DEFAULT true NOT NULL,
    fec_cre_rec timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    fec_act_rec timestamp(0) without time zone,
    fec_eli_rec timestamp(0) without time zone,
    CONSTRAINT recetas_estado_check CHECK (((est_val_rec)::text = ANY ((ARRAY['pendiente'::character varying, 'aprobada'::character varying, 'rechazada'::character varying, 'observada'::character varying])::text[]))),
    CONSTRAINT recetas_nivel_check CHECK (((niv_rec)::text = ANY ((ARRAY['facil'::character varying, 'medio'::character varying, 'dificil'::character varying])::text[]))),
    CONSTRAINT recetas_origen_check CHECK (((ori_rec)::text = ANY ((ARRAY['sistema'::character varying, 'ia_generada'::character varying, 'usuario'::character varying])::text[]))),
    CONSTRAINT recetas_porciones_check CHECK ((por_rec > 0)),
    CONSTRAINT recetas_tiempos_check CHECK (((tie_pre_rec >= 0) AND (tie_coc_rec >= 0))),
    CONSTRAINT recetas_verificada_check CHECK (((NOT ver_rec) OR ((est_val_rec)::text = 'aprobada'::text)))
);


ALTER TABLE public.recetas OWNER TO postgres;

--
-- TOC entry 5361 (class 0 OID 0)
-- Dependencies: 237
-- Name: COLUMN recetas.cod_usuario_rec; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.recetas.cod_usuario_rec IS 'Autor de la receta. NULL cuando es del sistema o la genero la IA.';


--
-- TOC entry 5362 (class 0 OID 0)
-- Dependencies: 237
-- Name: COLUMN recetas.tie_tot_rec; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.recetas.tie_tot_rec IS 'Columna calculada: se actualiza sola como tie_pre_rec + tie_coc_rec.';


--
-- TOC entry 5363 (class 0 OID 0)
-- Dependencies: 237
-- Name: COLUMN recetas.obs_nut_rec; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.recetas.obs_nut_rec IS 'Observaciones nutricionales registradas por el nutricionista.';


--
-- TOC entry 236 (class 1259 OID 105666)
-- Name: recetas_cod_receta_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.recetas_cod_receta_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.recetas_cod_receta_seq OWNER TO postgres;

--
-- TOC entry 5364 (class 0 OID 0)
-- Dependencies: 236
-- Name: recetas_cod_receta_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.recetas_cod_receta_seq OWNED BY public.recetas.cod_receta;


--
-- TOC entry 220 (class 1259 OID 105536)
-- Name: roles; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.roles (
    cod_rol smallint NOT NULL,
    nom_rol character varying(50) NOT NULL,
    des_rol character varying(255),
    act_rol boolean DEFAULT true NOT NULL,
    fec_cre_rol timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.roles OWNER TO postgres;

--
-- TOC entry 5365 (class 0 OID 0)
-- Dependencies: 220
-- Name: TABLE roles; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.roles IS 'Roles del sistema: Administrador, Nutricionista y Usuario.';


--
-- TOC entry 219 (class 1259 OID 105535)
-- Name: roles_cod_rol_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.roles_cod_rol_seq
    AS smallint
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.roles_cod_rol_seq OWNER TO postgres;

--
-- TOC entry 5366 (class 0 OID 0)
-- Dependencies: 219
-- Name: roles_cod_rol_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.roles_cod_rol_seq OWNED BY public.roles.cod_rol;


--
-- TOC entry 271 (class 1259 OID 113709)
-- Name: salidas_inventario; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.salidas_inventario (
    cod_salida bigint NOT NULL,
    cod_usuario_sal bigint NOT NULL,
    cod_ingrediente_sal bigint NOT NULL,
    cod_unidad_sal smallint NOT NULL,
    can_sal numeric(10,3) NOT NULL,
    mot_sal character varying(15) NOT NULL,
    cau_sal character varying(20),
    cod_receta_sal bigint,
    fec_cad_sal date,
    not_sal character varying(300),
    fec_sal timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    CONSTRAINT salidas_inventario_cantidad_check CHECK ((can_sal > (0)::numeric)),
    CONSTRAINT salidas_inventario_causa_check CHECK (((cau_sal IS NULL) OR ((cau_sal)::text = ANY ((ARRAY['caduco'::character varying, 'mal_estado'::character varying, 'exceso'::character varying, 'no_gusto'::character varying])::text[])))),
    CONSTRAINT salidas_inventario_causa_coherente_check CHECK (((((mot_sal)::text = 'desperdiciado'::text) AND (cau_sal IS NOT NULL)) OR (((mot_sal)::text <> 'desperdiciado'::text) AND (cau_sal IS NULL)))),
    CONSTRAINT salidas_inventario_motivo_check CHECK (((mot_sal)::text = ANY ((ARRAY['consumido'::character varying, 'desperdiciado'::character varying, 'otro'::character varying, 'sin_resolver'::character varying])::text[]))),
    CONSTRAINT salidas_inventario_receta_coherente_check CHECK (((cod_receta_sal IS NULL) OR ((mot_sal)::text = 'consumido'::text)))
);


ALTER TABLE public.salidas_inventario OWNER TO postgres;

--
-- TOC entry 5367 (class 0 OID 0)
-- Dependencies: 271
-- Name: TABLE salidas_inventario; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.salidas_inventario IS 'Todas las salidas de la despensa, con su motivo. Es la base de la tasa de aprovechamiento.';


--
-- TOC entry 5368 (class 0 OID 0)
-- Dependencies: 271
-- Name: COLUMN salidas_inventario.mot_sal; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.salidas_inventario.mot_sal IS 'consumido | desperdiciado | otro (regalado, devuelto) | sin_resolver (vencio y nadie respondio)';


--
-- TOC entry 5369 (class 0 OID 0)
-- Dependencies: 271
-- Name: COLUMN salidas_inventario.cau_sal; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.salidas_inventario.cau_sal IS 'Solo cuando mot_sal = desperdiciado: caduco | mal_estado | exceso | no_gusto';


--
-- TOC entry 5370 (class 0 OID 0)
-- Dependencies: 271
-- Name: COLUMN salidas_inventario.fec_cad_sal; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.salidas_inventario.fec_cad_sal IS 'La caducidad que tenia al salir, para medir el retraso entre vencer y resolverse.';


--
-- TOC entry 270 (class 1259 OID 113708)
-- Name: salidas_inventario_cod_salida_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.salidas_inventario_cod_salida_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.salidas_inventario_cod_salida_seq OWNER TO postgres;

--
-- TOC entry 5371 (class 0 OID 0)
-- Dependencies: 270
-- Name: salidas_inventario_cod_salida_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.salidas_inventario_cod_salida_seq OWNED BY public.salidas_inventario.cod_salida;


--
-- TOC entry 259 (class 1259 OID 105929)
-- Name: sessions; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    user_id bigint,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


ALTER TABLE public.sessions OWNER TO postgres;

--
-- TOC entry 229 (class 1259 OID 105608)
-- Name: unidades_medida; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.unidades_medida (
    cod_unidad smallint NOT NULL,
    nom_uni character varying(50) NOT NULL,
    abr_uni character varying(10) NOT NULL,
    tip_uni character varying(20) NOT NULL,
    fac_uni numeric(12,6) DEFAULT 1 NOT NULL,
    act_uni boolean DEFAULT true NOT NULL,
    CONSTRAINT unidades_medida_factor_check CHECK ((fac_uni > (0)::numeric)),
    CONSTRAINT unidades_medida_tipo_check CHECK (((tip_uni)::text = ANY ((ARRAY['masa'::character varying, 'volumen'::character varying, 'unidad'::character varying])::text[])))
);


ALTER TABLE public.unidades_medida OWNER TO postgres;

--
-- TOC entry 5372 (class 0 OID 0)
-- Dependencies: 229
-- Name: COLUMN unidades_medida.fac_uni; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.unidades_medida.fac_uni IS 'Factor de conversion hacia la unidad base de su tipo: masa a gramo, volumen a mililitro, unidad a pieza. Permite comparar el inventario del usuario contra lo que pide una receta aunque esten en unidades distintas.';


--
-- TOC entry 228 (class 1259 OID 105607)
-- Name: unidades_medida_cod_unidad_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.unidades_medida_cod_unidad_seq
    AS smallint
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.unidades_medida_cod_unidad_seq OWNER TO postgres;

--
-- TOC entry 5373 (class 0 OID 0)
-- Dependencies: 228
-- Name: unidades_medida_cod_unidad_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.unidades_medida_cod_unidad_seq OWNED BY public.unidades_medida.cod_unidad;


--
-- TOC entry 225 (class 1259 OID 105578)
-- Name: usuario_preferencias; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.usuario_preferencias (
    cod_usuario_upr bigint NOT NULL,
    cod_preferencia_upr smallint NOT NULL,
    fec_cre_upr timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.usuario_preferencias OWNER TO postgres;

--
-- TOC entry 224 (class 1259 OID 105558)
-- Name: usuarios; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.usuarios (
    cod_usuario bigint NOT NULL,
    cod_rol_usu smallint DEFAULT 3 NOT NULL,
    nom_usu character varying(100) NOT NULL,
    cor_usu character varying(255) NOT NULL,
    con_usu character varying(255) NOT NULL,
    fot_usu character varying(500),
    act_usu boolean DEFAULT true NOT NULL,
    ult_acc_usu timestamp(0) without time zone,
    remember_token character varying(100),
    fec_cre_usu timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    fec_act_usu timestamp(0) without time zone,
    fec_eli_usu timestamp(0) without time zone,
    CONSTRAINT usuarios_correo_check CHECK (((cor_usu)::text ~~ '%_@_%._%'::text))
);


ALTER TABLE public.usuarios OWNER TO postgres;

--
-- TOC entry 5374 (class 0 OID 0)
-- Dependencies: 224
-- Name: COLUMN usuarios.con_usu; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.usuarios.con_usu IS 'Contrasena encriptada con bcrypt.';


--
-- TOC entry 5375 (class 0 OID 0)
-- Dependencies: 224
-- Name: COLUMN usuarios.fec_eli_usu; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.usuarios.fec_eli_usu IS 'Fecha de baja logica. NULL significa que la cuenta sigue vigente.';


--
-- TOC entry 223 (class 1259 OID 105557)
-- Name: usuarios_cod_usuario_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.usuarios_cod_usuario_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.usuarios_cod_usuario_seq OWNER TO postgres;

--
-- TOC entry 5376 (class 0 OID 0)
-- Dependencies: 223
-- Name: usuarios_cod_usuario_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.usuarios_cod_usuario_seq OWNED BY public.usuarios.cod_usuario;


--
-- TOC entry 272 (class 1259 OID 113744)
-- Name: v_salidas_medidas; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW public.v_salidas_medidas AS
 SELECT s.cod_salida,
    s.cod_usuario_sal,
    s.cod_ingrediente_sal,
    i.nom_ing,
    s.mot_sal,
    s.cau_sal,
    s.can_sal,
    u.abr_uni,
    u.tip_uni,
    s.fec_cad_sal,
    s.fec_sal,
        CASE
            WHEN (s.fec_cad_sal IS NOT NULL) THEN ((s.fec_sal)::date - s.fec_cad_sal)
            ELSE NULL::integer
        END AS dias_de_retraso,
        CASE
            WHEN ((u.tip_uni)::text = 'masa'::text) THEN (s.can_sal * u.fac_uni)
            WHEN (((u.tip_uni)::text = 'volumen'::text) AND (i.den_ing IS NOT NULL)) THEN ((s.can_sal * u.fac_uni) * i.den_ing)
            ELSE NULL::numeric
        END AS gramos,
        CASE
            WHEN (i.pre_ing IS NULL) THEN NULL::numeric
            WHEN ((u.tip_uni)::text = 'masa'::text) THEN (((s.can_sal * u.fac_uni) / (1000)::numeric) * i.pre_ing)
            WHEN (((u.tip_uni)::text = 'volumen'::text) AND (i.den_ing IS NOT NULL)) THEN ((((s.can_sal * u.fac_uni) * i.den_ing) / (1000)::numeric) * i.pre_ing)
            ELSE NULL::numeric
        END AS bolivianos
   FROM ((public.salidas_inventario s
     JOIN public.ingredientes i ON ((i.cod_ingrediente = s.cod_ingrediente_sal)))
     JOIN public.unidades_medida u ON ((u.cod_unidad = s.cod_unidad_sal)));


ALTER VIEW public.v_salidas_medidas OWNER TO postgres;

--
-- TOC entry 5377 (class 0 OID 0)
-- Dependencies: 272
-- Name: VIEW v_salidas_medidas; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON VIEW public.v_salidas_medidas IS 'Cada salida con su peso en gramos y su valor en Bs, cuando se pueden calcular.';


--
-- TOC entry 273 (class 1259 OID 113749)
-- Name: v_aprovechamiento_usuario; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW public.v_aprovechamiento_usuario AS
 SELECT cod_usuario_sal,
    count(*) FILTER (WHERE ((mot_sal)::text = 'consumido'::text)) AS n_consumido,
    count(*) FILTER (WHERE ((mot_sal)::text = 'desperdiciado'::text)) AS n_desperdiciado,
    count(*) FILTER (WHERE ((mot_sal)::text = 'sin_resolver'::text)) AS n_sin_resolver,
    count(*) FILTER (WHERE ((mot_sal)::text = 'otro'::text)) AS n_otro,
    round(COALESCE(sum(gramos) FILTER (WHERE ((mot_sal)::text = 'consumido'::text)), (0)::numeric), 1) AS g_consumido,
    round(COALESCE(sum(gramos) FILTER (WHERE ((mot_sal)::text = 'desperdiciado'::text)), (0)::numeric), 1) AS g_desperdiciado,
    round(COALESCE(sum(bolivianos) FILTER (WHERE ((mot_sal)::text = 'desperdiciado'::text)), (0)::numeric), 2) AS bs_desperdiciado,
    count(*) FILTER (WHERE (gramos IS NULL)) AS sin_peso,
    round(((100.0 * COALESCE(sum(gramos) FILTER (WHERE ((mot_sal)::text = 'consumido'::text)), (0)::numeric)) / NULLIF(COALESCE(sum(gramos) FILTER (WHERE ((mot_sal)::text = ANY ((ARRAY['consumido'::character varying, 'desperdiciado'::character varying])::text[]))), (0)::numeric), (0)::numeric)), 1) AS tasa_por_peso,
    round(((100.0 * (count(*) FILTER (WHERE ((mot_sal)::text = 'consumido'::text)))::numeric) / (NULLIF(count(*) FILTER (WHERE ((mot_sal)::text = ANY ((ARRAY['consumido'::character varying, 'desperdiciado'::character varying])::text[]))), 0))::numeric), 1) AS tasa_por_registro
   FROM public.v_salidas_medidas m
  GROUP BY cod_usuario_sal;


ALTER VIEW public.v_aprovechamiento_usuario OWNER TO postgres;

--
-- TOC entry 5378 (class 0 OID 0)
-- Dependencies: 273
-- Name: VIEW v_aprovechamiento_usuario; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON VIEW public.v_aprovechamiento_usuario IS 'Tasa de aprovechamiento por usuario, por peso y por numero de registros.';


--
-- TOC entry 269 (class 1259 OID 106000)
-- Name: v_compatibilidad_recetas; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW public.v_compatibilidad_recetas AS
 WITH comparacion AS (
         SELECT u.cod_usuario,
            r.cod_receta,
            r.nom_rec,
            ir.opc_inr,
            i.nom_ing,
                CASE
                    WHEN ((ur.tip_uni)::text = 'masa'::text) THEN (ir.can_inr * ur.fac_uni)
                    WHEN (((ur.tip_uni)::text = 'volumen'::text) AND (i.den_ing IS NOT NULL)) THEN ((ir.can_inr * ur.fac_uni) * i.den_ing)
                    ELSE NULL::numeric
                END AS pide_gramos,
                CASE
                    WHEN ((uv.tip_uni)::text = 'masa'::text) THEN (inv.can_inv * uv.fac_uni)
                    WHEN (((uv.tip_uni)::text = 'volumen'::text) AND (i.den_ing IS NOT NULL)) THEN ((inv.can_inv * uv.fac_uni) * i.den_ing)
                    ELSE NULL::numeric
                END AS tiene_gramos,
            (ir.can_inr * ur.fac_uni) AS pide_base,
            (inv.can_inv * uv.fac_uni) AS tiene_base,
            ur.tip_uni AS tipo_receta,
            uv.tip_uni AS tipo_despensa,
            inv.cod_inventario
           FROM ((((((public.usuarios u
             CROSS JOIN public.recetas r)
             JOIN public.ingredientes_receta ir ON ((ir.cod_receta_inr = r.cod_receta)))
             JOIN public.ingredientes i ON ((i.cod_ingrediente = ir.cod_ingrediente_inr)))
             JOIN public.unidades_medida ur ON ((ur.cod_unidad = ir.cod_unidad_inr)))
             LEFT JOIN public.inventario inv ON (((inv.cod_ingrediente_inv = ir.cod_ingrediente_inr) AND (inv.cod_usuario_inv = u.cod_usuario) AND inv.act_inv)))
             LEFT JOIN public.unidades_medida uv ON ((uv.cod_unidad = inv.cod_unidad_inv)))
          WHERE (r.act_rec AND (r.fec_eli_rec IS NULL) AND u.act_usu)
        ), evaluada AS (
         SELECT comparacion.cod_usuario,
            comparacion.cod_receta,
            comparacion.nom_rec,
            comparacion.opc_inr,
            comparacion.nom_ing,
            comparacion.pide_gramos,
            comparacion.tiene_gramos,
            comparacion.pide_base,
            comparacion.tiene_base,
            comparacion.tipo_receta,
            comparacion.tipo_despensa,
            comparacion.cod_inventario,
                CASE
                    WHEN (comparacion.cod_inventario IS NULL) THEN false
                    WHEN ((comparacion.tipo_receta)::text = (comparacion.tipo_despensa)::text) THEN (comparacion.tiene_base >= comparacion.pide_base)
                    WHEN ((comparacion.pide_gramos IS NOT NULL) AND (comparacion.tiene_gramos IS NOT NULL)) THEN (comparacion.tiene_gramos >= comparacion.pide_gramos)
                    ELSE false
                END AS cubierto
           FROM comparacion
        )
 SELECT cod_usuario,
    cod_receta,
    nom_rec,
    count(*) FILTER (WHERE (NOT opc_inr)) AS ingredientes_requeridos,
    count(*) FILTER (WHERE ((NOT opc_inr) AND cubierto)) AS ingredientes_cubiertos,
    round(((100.0 * (count(*) FILTER (WHERE ((NOT opc_inr) AND cubierto)))::numeric) / (NULLIF(count(*) FILTER (WHERE (NOT opc_inr)), 0))::numeric), 2) AS compatibilidad_pct,
    array_agg(nom_ing ORDER BY nom_ing) FILTER (WHERE ((NOT opc_inr) AND (NOT cubierto))) AS ingredientes_faltantes
   FROM evaluada
  GROUP BY cod_usuario, cod_receta, nom_rec;


ALTER VIEW public.v_compatibilidad_recetas OWNER TO postgres;

--
-- TOC entry 5379 (class 0 OID 0)
-- Dependencies: 269
-- Name: VIEW v_compatibilidad_recetas; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON VIEW public.v_compatibilidad_recetas IS 'Compatibilidad entre la despensa de cada usuario y cada receta activa, convirtiendo volumen a masa mediante la densidad del ingrediente.';


--
-- TOC entry 254 (class 1259 OID 105881)
-- Name: validaciones_receta; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.validaciones_receta (
    cod_validacion bigint NOT NULL,
    cod_receta_vre bigint NOT NULL,
    cod_usuario_vre bigint NOT NULL,
    est_vre character varying(15) NOT NULL,
    obs_vre text,
    val_ing_vre boolean DEFAULT false NOT NULL,
    val_pas_vre boolean DEFAULT false NOT NULL,
    val_nut_vre boolean DEFAULT false NOT NULL,
    fec_rev_vre timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    CONSTRAINT validaciones_receta_estado_check CHECK (((est_vre)::text = ANY ((ARRAY['aprobada'::character varying, 'rechazada'::character varying, 'observada'::character varying])::text[])))
);


ALTER TABLE public.validaciones_receta OWNER TO postgres;

--
-- TOC entry 5380 (class 0 OID 0)
-- Dependencies: 254
-- Name: TABLE validaciones_receta; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.validaciones_receta IS 'Historias de Usuario 9, 10, 12 y 13. Guarda cada revision del nutricionista.';


--
-- TOC entry 5381 (class 0 OID 0)
-- Dependencies: 254
-- Name: COLUMN validaciones_receta.val_ing_vre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.validaciones_receta.val_ing_vre IS 'Los ingredientes fueron validados.';


--
-- TOC entry 5382 (class 0 OID 0)
-- Dependencies: 254
-- Name: COLUMN validaciones_receta.val_pas_vre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.validaciones_receta.val_pas_vre IS 'Los pasos de preparacion fueron validados.';


--
-- TOC entry 5383 (class 0 OID 0)
-- Dependencies: 254
-- Name: COLUMN validaciones_receta.val_nut_vre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.validaciones_receta.val_nut_vre IS 'La informacion nutricional fue validada.';


--
-- TOC entry 253 (class 1259 OID 105880)
-- Name: validaciones_receta_cod_validacion_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.validaciones_receta_cod_validacion_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.validaciones_receta_cod_validacion_seq OWNER TO postgres;

--
-- TOC entry 5384 (class 0 OID 0)
-- Dependencies: 253
-- Name: validaciones_receta_cod_validacion_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.validaciones_receta_cod_validacion_seq OWNED BY public.validaciones_receta.cod_validacion;


--
-- TOC entry 250 (class 1259 OID 105838)
-- Name: valoraciones; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.valoraciones (
    cod_valoracion bigint NOT NULL,
    cod_usuario_val bigint NOT NULL,
    cod_receta_val bigint NOT NULL,
    pun_val smallint NOT NULL,
    com_val text,
    fec_cre_val timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    fec_act_val timestamp(0) without time zone,
    CONSTRAINT valoraciones_puntaje_check CHECK (((pun_val >= 1) AND (pun_val <= 5)))
);


ALTER TABLE public.valoraciones OWNER TO postgres;

--
-- TOC entry 249 (class 1259 OID 105837)
-- Name: valoraciones_cod_valoracion_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.valoraciones_cod_valoracion_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.valoraciones_cod_valoracion_seq OWNER TO postgres;

--
-- TOC entry 5385 (class 0 OID 0)
-- Dependencies: 249
-- Name: valoraciones_cod_valoracion_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.valoraciones_cod_valoracion_seq OWNED BY public.valoraciones.cod_valoracion;


--
-- TOC entry 4954 (class 2604 OID 105909)
-- Name: bitacora cod_bitacora; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.bitacora ALTER COLUMN cod_bitacora SET DEFAULT nextval('public.bitacora_cod_bitacora_seq'::regclass);


--
-- TOC entry 4901 (class 2604 OID 105598)
-- Name: categorias_ingrediente cod_categoria_ingrediente; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categorias_ingrediente ALTER COLUMN cod_categoria_ingrediente SET DEFAULT nextval('public.categorias_ingrediente_cod_categoria_ingrediente_seq'::regclass);


--
-- TOC entry 4911 (class 2604 OID 105645)
-- Name: categorias_receta cod_categoria_receta; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categorias_receta ALTER COLUMN cod_categoria_receta SET DEFAULT nextval('public.categorias_receta_cod_categoria_receta_seq'::regclass);


--
-- TOC entry 4914 (class 2604 OID 105658)
-- Name: etiquetas cod_etiqueta; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.etiquetas ALTER COLUMN cod_etiqueta SET DEFAULT nextval('public.etiquetas_cod_etiqueta_seq'::regclass);


--
-- TOC entry 4959 (class 2604 OID 105985)
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- TOC entry 4947 (class 2604 OID 105864)
-- Name: favoritos cod_favorito; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.favoritos ALTER COLUMN cod_favorito SET DEFAULT nextval('public.favoritos_cod_favorito_seq'::regclass);


--
-- TOC entry 4938 (class 2604 OID 105800)
-- Name: historial_consulta cod_consulta; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.historial_consulta ALTER COLUMN cod_consulta SET DEFAULT nextval('public.historial_consulta_cod_consulta_seq'::regclass);


--
-- TOC entry 4941 (class 2604 OID 105817)
-- Name: historial_resultado cod_resultado; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.historial_resultado ALTER COLUMN cod_resultado SET DEFAULT nextval('public.historial_resultado_cod_resultado_seq'::regclass);


--
-- TOC entry 4907 (class 2604 OID 105625)
-- Name: ingredientes cod_ingrediente; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ingredientes ALTER COLUMN cod_ingrediente SET DEFAULT nextval('public.ingredientes_cod_ingrediente_seq'::regclass);


--
-- TOC entry 4928 (class 2604 OID 105708)
-- Name: ingredientes_receta cod_ingrediente_receta; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ingredientes_receta ALTER COLUMN cod_ingrediente_receta SET DEFAULT nextval('public.ingredientes_receta_cod_ingrediente_receta_seq'::regclass);


--
-- TOC entry 4934 (class 2604 OID 105770)
-- Name: inventario cod_inventario; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inventario ALTER COLUMN cod_inventario SET DEFAULT nextval('public.inventario_cod_inventario_seq'::regclass);


--
-- TOC entry 4958 (class 2604 OID 105975)
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- TOC entry 4956 (class 2604 OID 105926)
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- TOC entry 4931 (class 2604 OID 105736)
-- Name: pasos_receta cod_paso; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pasos_receta ALTER COLUMN cod_paso SET DEFAULT nextval('public.pasos_receta_cod_paso_seq'::regclass);


--
-- TOC entry 4957 (class 2604 OID 105949)
-- Name: personal_access_tokens id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.personal_access_tokens ALTER COLUMN id SET DEFAULT nextval('public.personal_access_tokens_id_seq'::regclass);


--
-- TOC entry 4893 (class 2604 OID 105550)
-- Name: preferencias_dieta cod_preferencia; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.preferencias_dieta ALTER COLUMN cod_preferencia SET DEFAULT nextval('public.preferencias_dieta_cod_preferencia_seq'::regclass);


--
-- TOC entry 4917 (class 2604 OID 105670)
-- Name: recetas cod_receta; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.recetas ALTER COLUMN cod_receta SET DEFAULT nextval('public.recetas_cod_receta_seq'::regclass);


--
-- TOC entry 4890 (class 2604 OID 105539)
-- Name: roles cod_rol; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.roles ALTER COLUMN cod_rol SET DEFAULT nextval('public.roles_cod_rol_seq'::regclass);


--
-- TOC entry 4961 (class 2604 OID 113712)
-- Name: salidas_inventario cod_salida; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.salidas_inventario ALTER COLUMN cod_salida SET DEFAULT nextval('public.salidas_inventario_cod_salida_seq'::regclass);


--
-- TOC entry 4904 (class 2604 OID 105611)
-- Name: unidades_medida cod_unidad; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.unidades_medida ALTER COLUMN cod_unidad SET DEFAULT nextval('public.unidades_medida_cod_unidad_seq'::regclass);


--
-- TOC entry 4896 (class 2604 OID 105561)
-- Name: usuarios cod_usuario; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuarios ALTER COLUMN cod_usuario SET DEFAULT nextval('public.usuarios_cod_usuario_seq'::regclass);


--
-- TOC entry 4949 (class 2604 OID 105884)
-- Name: validaciones_receta cod_validacion; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.validaciones_receta ALTER COLUMN cod_validacion SET DEFAULT nextval('public.validaciones_receta_cod_validacion_seq'::regclass);


--
-- TOC entry 4945 (class 2604 OID 105841)
-- Name: valoraciones cod_valoracion; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.valoraciones ALTER COLUMN cod_valoracion SET DEFAULT nextval('public.valoraciones_cod_valoracion_seq'::regclass);


--
-- TOC entry 5308 (class 0 OID 105906)
-- Dependencies: 256
-- Data for Name: bitacora; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.bitacora (cod_bitacora, cod_usuario_bit, acc_bit, tab_bit, reg_bit, det_bit, ip_bit, fec_cre_bit) FROM stdin;
1	2	cierre_sesion	usuarios	2	\N	127.0.0.1	2026-09-29 16:39:36
2	1	inicio_sesion	usuarios	1	\N	127.0.0.1	2026-09-29 16:39:49
3	1	cierre_sesion	usuarios	1	\N	127.0.0.1	2026-09-29 16:42:21
4	1	inicio_sesion	usuarios	1	\N	127.0.0.1	2026-09-29 17:06:28
5	1	cierre_sesion	usuarios	1	\N	127.0.0.1	2026-09-29 17:10:49
6	1	inicio_sesion	usuarios	1	\N	127.0.0.1	2026-09-29 17:17:00
7	1	cierre_sesion	usuarios	1	\N	127.0.0.1	2026-09-29 17:27:10
8	1	inicio_sesion	usuarios	1	\N	127.0.0.1	2026-09-29 17:33:32
9	1	cierre_sesion	usuarios	1	\N	127.0.0.1	2026-09-29 18:12:21
10	1	inicio_sesion	usuarios	1	\N	127.0.0.1	2026-09-29 18:12:32
11	1	inicio_sesion	usuarios	1	\N	127.0.0.1	2026-09-29 23:00:29
12	1	cierre_sesion	usuarios	1	\N	127.0.0.1	2026-09-29 23:45:42
13	1	inicio_sesion	usuarios	1	\N	127.0.0.1	2026-09-30 08:25:07
14	1	agregar_inventario	inventario	1	{"ingrediente": "Coliflor"}	127.0.0.1	2026-09-30 08:43:03
15	1	generar_recetas	historial_consulta	1	{"ms": 29, "resultados": 0, "ingredientes": 1}	127.0.0.1	2026-09-30 08:43:31
16	1	editar_perfil	usuarios	1	\N	127.0.0.1	2026-09-30 08:44:22
17	1	generar_recetas	historial_consulta	2	{"ms": 21, "resultados": 0, "ingredientes": 1}	127.0.0.1	2026-09-30 08:45:01
18	1	cierre_sesion	usuarios	1	\N	127.0.0.1	2026-09-30 08:46:37
19	1	inicio_sesion	usuarios	1	\N	127.0.0.1	2026-09-30 15:49:07
20	1	cierre_sesion	usuarios	1	\N	127.0.0.1	2026-09-30 15:59:46
21	1	inicio_sesion	usuarios	1	\N	127.0.0.1	2026-09-30 16:02:03
22	1	cambio_factor	unidades_medida	5	{"antes": 28.3495, "despues": 28.349527}	127.0.0.1	2026-09-30 16:03:34
23	1	editar	unidades_medida	5	\N	127.0.0.1	2026-09-30 16:03:34
24	1	inicio_sesion	usuarios	1	\N	127.0.0.1	2026-10-01 17:58:25
25	1	generar_recetas	historial_consulta	3	{"ms": 34, "resultados": 0, "ingredientes": 1}	127.0.0.1	2026-10-01 19:17:58
26	1	generar_recetas	historial_consulta	4	{"ms": 42, "resultados": 0, "ingredientes": 1}	127.0.0.1	2026-10-01 19:21:54
27	1	generar_recetas	historial_consulta	5	{"ms": 47, "resultados": 0, "ingredientes": 1}	127.0.0.1	2026-10-01 19:26:03
28	1	generar_recetas	historial_consulta	6	{"ms": 40, "resultados": 0, "ingredientes": 1}	127.0.0.1	2026-10-01 19:27:13
29	3	inicio_sesion	usuarios	3	\N	127.0.0.1	2026-10-01 19:35:27
30	3	inicio_sesion	usuarios	3	\N	127.0.0.1	2026-10-01 19:37:05
31	3	agregar_inventario	inventario	2	{"ingrediente": "Acelga"}	127.0.0.1	2026-10-01 19:37:09
32	3	agregar_inventario	inventario	3	{"ingrediente": "Vino tinto"}	127.0.0.1	2026-10-01 19:37:11
33	3	agregar_inventario	inventario	4	{"ingrediente": "Cebolla"}	127.0.0.1	2026-10-01 19:37:13
34	3	generar_recetas	historial_consulta	7	{"ms": 27, "resultados": 1, "ingredientes": 3}	127.0.0.1	2026-10-01 19:37:22
35	3	vaciar_inventario	inventario	\N	{"cantidad": 3}	127.0.0.1	2026-10-01 19:37:27
36	1	cierre_sesion	usuarios	1	\N	127.0.0.1	2026-10-01 19:39:06
37	1	inicio_sesion	usuarios	1	\N	127.0.0.1	2026-10-06 22:06:24
38	1	generar_recetas	historial_consulta	8	{"ms": 29, "resultados": 0, "ingredientes": 1}	127.0.0.1	2026-10-06 22:07:01
39	1	generar_recetas	historial_consulta	9	{"ms": 61, "resultados": 0, "ingredientes": 1}	127.0.0.1	2026-10-06 22:17:34
40	1	generar_recetas	historial_consulta	10	{"ms": 42, "resultados": 0, "ingredientes": 1}	127.0.0.1	2026-10-06 22:19:04
41	1	proponer_receta_ia	\N	\N	{"nombre": "Coliflor ahogado con ají colorado", "ingredientes": 7, "usa_de_despensa": 1}	127.0.0.1	2026-10-06 22:19:55
42	1	guardar_receta_ia	recetas	32	{"nombre": "Coliflor ahogado con ají colorado"}	127.0.0.1	2026-10-06 22:19:55
43	1	generar_recetas	historial_consulta	11	{"ms": 89, "resultados": 0, "ingredientes": 1}	127.0.0.1	2026-10-06 22:28:54
44	1	proponer_receta_ia	\N	\N	{"nombre": "Coliflor rebosada al ají colorado", "ingredientes": 8, "usa_de_despensa": 1}	127.0.0.1	2026-10-06 22:29:37
45	1	generar_recetas	historial_consulta	12	{"ms": 90, "resultados": 0, "ingredientes": 1}	127.0.0.1	2026-10-06 22:39:12
46	1	proponer_receta_ia	\N	\N	{"recetas": ["Coliflor frito crujiente con ají amarillo", "Sopa ligera de coliflor al comino", "Coliflor al horno con ají colorado y ajo", "Guiso rápido de coliflor con pimienta y perejil"], "descartadas": 1}	127.0.0.1	2026-10-06 22:40:23
47	1	guardar_receta_ia	recetas	33	{"nombre": "Coliflor frito crujiente con ají amarillo"}	127.0.0.1	2026-10-06 22:40:24
48	1	guardar_receta_ia	recetas	34	{"nombre": "Coliflor al horno con ají colorado y ajo"}	127.0.0.1	2026-10-06 22:40:24
49	1	agregar_inventario	inventario	5	{"ingrediente": "Remolacha"}	127.0.0.1	2026-10-06 22:43:38
50	1	generar_recetas	historial_consulta	13	{"ms": 59, "resultados": 0, "ingredientes": 2}	127.0.0.1	2026-10-06 22:43:48
51	1	proponer_receta_ia	\N	\N	{"recetas": ["Guiso especiado de remolacha y coliflor"], "descartadas": 4}	127.0.0.1	2026-10-06 22:45:30
52	1	cierre_sesion	usuarios	1	\N	127.0.0.1	2026-10-06 22:53:54
53	2	inicio_sesion	usuarios	2	\N	127.0.0.1	2026-10-06 22:54:57
54	2	cierre_sesion	usuarios	2	\N	127.0.0.1	2026-10-06 22:55:27
55	3	inicio_sesion	usuarios	3	\N	127.0.0.1	2026-10-06 22:55:48
56	1	inicio_sesion	usuarios	1	\N	127.0.0.1	2026-10-07 14:04:52
57	1	cierre_sesion	usuarios	1	\N	127.0.0.1	2026-10-07 14:33:33
58	1	inicio_sesion	usuarios	1	\N	127.0.0.1	2026-10-07 14:59:20
59	1	crear	preferencias_dieta	10	{"nombre": "11"}	127.0.0.1	2026-10-07 15:05:27
60	1	eliminar	preferencias_dieta	10	\N	127.0.0.1	2026-10-07 15:05:34
61	1	editar	categorias_ingrediente	12	\N	127.0.0.1	2026-10-07 15:05:51
62	1	crear	categorias_ingrediente	21	{"nombre": "123"}	127.0.0.1	2026-10-07 15:06:01
63	1	eliminar	categorias_ingrediente	21	\N	127.0.0.1	2026-10-07 15:06:06
64	1	cierre_sesion	usuarios	1	\N	127.0.0.1	2026-10-07 15:07:42
65	3	inicio_sesion	usuarios	3	\N	127.0.0.1	2026-10-07 15:07:59
66	3	agregar_inventario	inventario	18	{"ingrediente": "Pimiento morrón"}	127.0.0.1	2026-10-07 15:08:43
67	3	agregar_inventario	inventario	19	{"ingrediente": "Pechuga de pollo"}	127.0.0.1	2026-10-07 15:08:59
68	3	generar_recetas	historial_consulta	14	{"ms": 34, "resultados": 0, "ingredientes": 2}	127.0.0.1	2026-10-07 15:15:37
69	3	proponer_receta_ia	\N	\N	{"recetas": ["Pollo a la plancha con pimiento morrón", "Ají de pollo con pimiento", "Sopa casera de pollo con morrón", "Pollo salteado al pimiento", "Ensalada tibia de pollo y morrón asado"], "descartadas": 0}	127.0.0.1	2026-10-07 15:17:16
70	3	guardar_receta_ia	recetas	35	{"nombre": "Ají de pollo con pimiento"}	127.0.0.1	2026-10-07 15:18:48
71	3	guardar_receta_ia	recetas	36	{"nombre": "Sopa casera de pollo con morrón"}	127.0.0.1	2026-10-07 15:19:01
72	3	cierre_sesion	usuarios	3	\N	127.0.0.1	2026-10-07 15:20:43
73	1	inicio_sesion	usuarios	1	\N	127.0.0.1	2026-10-07 15:46:55
74	1	editar	ingredientes	89	\N	127.0.0.1	2026-10-07 15:49:39
75	1	editar	ingredientes	88	\N	127.0.0.1	2026-10-07 15:50:54
76	1	editar	ingredientes	10	\N	127.0.0.1	2026-10-07 15:51:31
77	1	editar	ingredientes	121	\N	127.0.0.1	2026-10-07 15:52:25
78	1	editar	ingredientes	6	\N	127.0.0.1	2026-10-07 15:53:25
79	1	editar	ingredientes	94	\N	127.0.0.1	2026-10-07 15:54:08
80	1	editar	ingredientes	95	\N	127.0.0.1	2026-10-07 15:55:21
81	1	editar	ingredientes	96	\N	127.0.0.1	2026-10-07 15:56:04
82	1	editar	ingredientes	85	\N	127.0.0.1	2026-10-07 16:16:49
83	1	editar	ingredientes	114	\N	127.0.0.1	2026-10-07 16:18:34
84	1	editar	ingredientes	71	\N	127.0.0.1	2026-10-07 16:19:24
85	1	editar	ingredientes	99	\N	127.0.0.1	2026-10-07 16:20:19
86	1	editar	ingredientes	17	\N	127.0.0.1	2026-10-07 16:21:08
87	1	cierre_sesion	usuarios	1	\N	127.0.0.1	2026-10-07 16:25:58
88	\N	registro_usuario	usuarios	4	{"correo": "Mariela@gmail.com"}	127.0.0.1	2026-10-08 14:43:25
89	\N	registro_usuario	usuarios	5	{"correo": "Marielas@gmail.com"}	127.0.0.1	2026-10-08 14:46:50
90	5	cierre_sesion	usuarios	5	\N	127.0.0.1	2026-10-08 14:47:20
91	1	inicio_sesion	usuarios	1	\N	127.0.0.1	2026-10-08 14:47:53
92	1	identificar_ingrediente_ia	ingredientes	\N	{"texto": "zapato", "comestible": false}	127.0.0.1	2026-10-08 15:22:21
96	1	identificar_ingrediente_ia	ingredientes	\N	{"texto": "zapato", "comestible": false}	127.0.0.1	2026-10-08 15:25:45
97	1	identificar_ingrediente_ia	ingredientes	\N	{"texto": "ropa", "comestible": false}	127.0.0.1	2026-10-08 15:30:05
98	1	identificar_ingrediente_ia	ingredientes	\N	{"texto": "ropas", "comestible": false}	127.0.0.1	2026-10-08 15:31:16
99	1	identificar_ingrediente_ia	ingredientes	\N	{"texto": "ropa", "comestible": false}	127.0.0.1	2026-10-08 15:31:39
100	1	identificar_ingrediente_ia	ingredientes	\N	{"texto": "zapato", "comestible": false}	127.0.0.1	2026-10-08 15:35:39
101	1	identificar_ingrediente_ia	ingredientes	\N	{"texto": "anillo", "comestible": false}	127.0.0.1	2026-10-08 15:37:44
102	1	crear	ingredientes	126	{"nombre": "ZAPATO"}	127.0.0.1	2026-10-08 15:40:54
103	1	eliminar	ingredientes	126	\N	127.0.0.1	2026-10-08 15:41:25
109	1	nutricion_ingrediente_ia	ingredientes	\N	{"nombre": "ZAPATO"}	127.0.0.1	2026-10-08 15:50:27
120	1	nutricion_ingrediente_ia	\N	\N	{"unidad": "Onzas"}	127.0.0.1	2026-10-08 16:13:02
125	1	validar_receta	recetas	2	{"receta": "Pique macho", "dictamen": "aprobada", "verificada": true}	127.0.0.1	2026-10-08 16:39:10
126	1	inicio_sesion	usuarios	1	\N	127.0.0.1	2026-10-09 11:55:19
127	1	identificar_ingrediente_ia	ingredientes	\N	{"texto": "zapato", "comestible": false}	127.0.0.1	2026-10-09 11:57:25
128	1	identificar_ingrediente_ia	ingredientes	\N	{"texto": "perro", "comestible": false}	127.0.0.1	2026-10-09 11:58:06
129	1	identificar_ingrediente_ia	ingredientes	\N	{"texto": "paloma", "comestible": true}	127.0.0.1	2026-10-09 11:58:54
130	1	crear_ingrediente_ia	ingredientes	128	{"texto": "paloma", "nombre": "Paloma"}	127.0.0.1	2026-10-09 11:58:54
131	1	identificar_ingrediente_ia	ingredientes	\N	{"texto": "cuy", "comestible": true}	127.0.0.1	2026-10-09 12:00:35
132	1	crear_ingrediente_ia	ingredientes	129	{"texto": "cuy", "nombre": "Cuy"}	127.0.0.1	2026-10-09 12:00:35
133	1	identificar_ingrediente_ia	ingredientes	\N	{"texto": "humano", "comestible": false}	127.0.0.1	2026-10-09 12:01:40
134	1	generar_recetas	historial_consulta	15	{"ms": 35, "resultados": 0, "ingredientes": 2}	127.0.0.1	2026-10-09 12:02:00
135	1	proponer_receta_ia	\N	\N	{"recetas": ["Sopa reconfortante de coliflor y remolacha con huacataya", "Ají criollo de coliflor y remolacha", "Ensalada tibia de coliflor y remolacha al orégano", "Coliflor y remolacha asadas al palillo", "Salteado rápido de coliflor y remolacha al ají amarillo"], "descartadas": 0}	127.0.0.1	2026-10-09 12:03:45
136	1	proponer_receta_ia	\N	\N	{"recetas": ["Coliflor al horno con ají amarillo", "Sopa liviana de coliflor y comino", "Ensalada cruda de remolacha y coliflor", "Guiso rápido de remolacha especiada", "Salteado crujiente de coliflor y remolacha"], "descartadas": 0}	127.0.0.1	2026-10-09 12:04:15
137	1	proponer_receta_ia	\N	\N	{"recetas": ["Sopa nutritiva de coliflor y remolacha", "Ensalada tibia de remolacha y coliflor a la huacataya", "Ají criollo de coliflor y remolacha", "Salteado a la plancha de coliflor y remolacha al ajo", "Escabeche aromático de coliflor y remolacha"], "descartadas": 0}	127.0.0.1	2026-10-09 12:05:38
138	1	proponer_receta_ia	\N	\N	{"recetas": ["Ensalada cruda de remolacha y coliflor"], "descartadas": 4}	127.0.0.1	2026-10-09 12:06:40
139	1	proponer_receta_ia	\N	\N	{"recetas": ["Ensalada cruda de remolacha y coliflor"], "descartadas": 4}	127.0.0.1	2026-10-09 12:07:55
140	1	proponer_receta_ia	\N	\N	{"recetas": ["Guiso aromático de remolacha y coliflor", "Coliflor al horno con especias y remolacha"], "descartadas": 3}	127.0.0.1	2026-10-09 12:08:36
141	1	proponer_receta_ia	\N	\N	{"recetas": ["Ensalada cruda de remolacha y coliflor"], "descartadas": 4}	127.0.0.1	2026-10-09 12:08:56
142	3	inicio_sesion	usuarios	3	\N	127.0.0.1	2026-10-09 16:31:59
143	3	quitar_inventario	inventario	\N	{"ingrediente": "Tomate"}	127.0.0.1	2026-10-09 16:32:05
144	3	quitar_inventario	inventario	\N	{"ingrediente": "Zanahoria"}	127.0.0.1	2026-10-09 16:32:06
145	\N	cerrar_salidas_pendientes	inventario	\N	{"quitadas": 2, "registradas": 2}	127.0.0.1	2026-10-09 16:32:22
146	3	inicio_sesion	usuarios	3	\N	127.0.0.1	2026-10-09 16:32:42
147	1	inicio_sesion	usuarios	1	\N	127.0.0.1	2026-10-09 16:33:04
148	3	inicio_sesion	usuarios	3	\N	127.0.0.1	2026-10-09 16:33:33
149	3	inicio_sesion	usuarios	3	\N	127.0.0.1	2026-10-09 16:34:13
\.


--
-- TOC entry 5315 (class 0 OID 105957)
-- Dependencies: 263
-- Data for Name: cache; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.cache (key, value, expiration) FROM stdin;
sazon-cache-roster:project:v3:cc01525ed8a55eb16bed1c99d2da3725	TzoyNjoiTGFyYXZlbFxSb3N0ZXJcUHJvamVjdFNjYW4iOjg6e3M6ODoiYmFzZVBhdGgiO3M6MjI6IkM6XGxhcmFnb25cd3d3XGNvbWlkYVwiO3M6MzoicGhwIjtPOjM1OiJMYXJhdmVsXFJvc3RlclxFY29zeXN0ZW1zXEVjb3N5c3RlbSI6Mjp7czo5OiIAKgBieU5hbWUiO2E6MTE2OntzOjEwOiJicmljay9tYXRoIjtPOjIyOiJMYXJhdmVsXFJvc3RlclxQYWNrYWdlIjo3OntzOjc6IgAqAG5hbWUiO3M6MTA6ImJyaWNrL21hdGgiO3M6MTA6IgAqAHZlcnNpb24iO3M6NToiMS4wLjAiO3M6OToiACoAc291cmNlIjtFOjQzOiJMYXJhdmVsXFJvc3RlclxFbnVtc1xQYWNrYWdlU291cmNlOkNvbXBvc2VyIjtzOjY6IgAqAGRldiI7YjowO3M6OToiACoAZGlyZWN0IjtiOjA7czoxMzoiACoAY29uc3RyYWludCI7czowOiIiO3M6NzoiACoAcGF0aCI7czozOToiQzpcbGFyYWdvblx3d3dcY29taWRhXHZlbmRvclxicmlja1xtYXRoIjt9czozMToiY2FyYm9ucGhwL2NhcmJvbi1kb2N0cmluZS10eXBlcyI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjMxOiJjYXJib25waHAvY2FyYm9uLWRvY3RyaW5lLXR5cGVzIjtzOjEwOiIAKgB2ZXJzaW9uIjtzOjU6IjMuMi4xIjtzOjk6IgAqAHNvdXJjZSI7cjo4O3M6NjoiACoAZGV2IjtiOjA7czo5OiIAKgBkaXJlY3QiO2I6MDtzOjEzOiIAKgBjb25zdHJhaW50IjtzOjA6IiI7czo3OiIAKgBwYXRoIjtzOjYwOiJDOlxsYXJhZ29uXHd3d1xjb21pZGFcdmVuZG9yXGNhcmJvbnBocFxjYXJib24tZG9jdHJpbmUtdHlwZXMiO31zOjIzOiJkZmx5ZGV2L2RvdC1hY2Nlc3MtZGF0YSI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjIzOiJkZmx5ZGV2L2RvdC1hY2Nlc3MtZGF0YSI7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cG9seWZpbGwtaW50bC1ncmFwaGVtZSI7czoxMDoiACoAdmVyc2lvbiI7czo2OiIxLjQxLjAiO3M6OToiACoAc291cmNlIjtyOjg7czo2OiIAKgBkZXYiO2I6MDtzOjk6IgAqAGRpcmVjdCI7YjowO3M6MTM6IgAqAGNvbnN0cmFpbnQiO3M6MDoiIjtzOjc6IgAqAHBhdGgiO3M6NTk6IkM6XGxhcmFnb25cd3d3XGNvbWlkYVx2ZW5kb3Jcc3ltZm9ueVxwb2x5ZmlsbC1pbnRsLWdyYXBoZW1lIjt9czoyNToic3ltZm9ueS9wb2x5ZmlsbC1pbnRsLWlkbiI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjI1OiJzeW1mb255L3BvbHlmaWxsLWludGwtaWRuIjtzOjEwOiIAKgB2ZXJzaW9uIjtzOjY6IjEuNDIuMCI7czo5OiIAKgBzb3VyY2UiO3I6ODtzOjY6IgAqAGRldiI7YjowO3M6OToiACoAZGlyZWN0IjtiOjA7czoxMzoiACoAY29uc3RyYWludCI7czowOiIiO3M6NzoiACoAcGF0aCI7czo1NDoiQzpcbGFyYWdvblx3d3dcY29taWRhXHZlbmRvclxzeW1mb255XHBvbHlmaWxsLWludGwtaWRuIjt9czozMjoic3ltZm9ueS9wb2x5ZmlsbC1pbnRsLW5vcm1hbGl6ZXIiO086MjI6IkxhcmF2ZWxcUm9zdGVyXFBhY2thZ2UiOjc6e3M6NzoiACoAbmFtZSI7czozMjoic3ltZm9ueS9wb2x5ZmlsbC1pbnRsLW5vcm1hbGl6ZXIiO3M6MTA6IgAqAHZlcnNpb24iO3M6NjoiMS40Mi4wIjtzOjk6IgAqAHNvdXJjZSI7cjo4O3M6NjoiACoAZGV2IjtiOjA7czo5OiIAKgBkaXJlY3QiO2I6MDtzOjEzOiIAKgBjb25zdHJhaW50IjtzOjA6IiI7czo3OiIAKgBwYXRoIjtzOjYxOiJDOlxsYXJhZ29uXHd3d1xjb21pZGFcdmVuZG9yXHN5bWZvbnlccG9seWZpbGwtaW50bC1ub3JtYWxpemVyIjt9czoyNToic3ltZm9ueS9wb2x5ZmlsbC1tYnN0cmluZyI7TzoyMjoiTGFyYXZl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NztpOjYwO3I6NDg1O2k6NjE7cjo0OTM7aTo2MjtyOjUwMTtpOjYzO3I6NTA5O2k6NjQ7cjo1MTc7aTo2NTtyOjUyNTtpOjY2O3I6NTMzO2k6Njc7cjo1NDE7aTo2ODtyOjU0OTtpOjY5O3I6NTU3O2k6NzA7cjo1NjU7aTo3MTtyOjU3MztpOjcyO3I6NTgxO2k6NzM7cjo1ODk7aTo3NDtyOjU5NztpOjc1O3I6NjA1O2k6NzY7cjo2MTM7aTo3NztyOjYyMTtpOjc4O3I6NjI5O2k6Nzk7cjo2Mzc7aTo4MDtyOjY0NTtpOjgxO3I6NjUzO2k6ODI7cjo2NjE7aTo4MztyOjY2OTtpOjg0O3I6Njc3O2k6ODU7cjo2ODU7aTo4NjtyOjY5MztpOjg3O3I6NzAxO2k6ODg7cjo3MDk7aTo4OTtyOjcxNztpOjkwO3I6NzI1O2k6OTE7cjo3MzM7aTo5MjtyOjc0MTtpOjkzO3I6NzQ5O2k6OTQ7cjo3NTc7aTo5NTtyOjc2NTtpOjk2O3I6NzczO2k6OTc7cjo3ODE7aTo5ODtyOjc4OTtpOjk5O3I6Nzk3O2k6MTAwO3I6ODA1O2k6MTAxO3I6ODEzO2k6MTAyO3I6ODIxO2k6MTAzO3I6ODI5O2k6MTA0O3I6ODM3O2k6MTA1O3I6ODQ1O2k6MTA2O3I6ODUzO2k6MTA3O3I6ODYxO2k6MTA4O3I6ODY5O2k6MTA5O3I6ODc3O2k6MTEwO3I6ODg1O2k6MTExO3I6ODkzO2k6MTEyO3I6OTAxO2k6MTEzO3I6OTA5O2k6MTE0O3I6OTE3O2k6MTE1O3I6OTI1O31zOjI4OiIAKgBlc2NhcGVXaGVuQ2FzdGluZ1RvU3RyaW5nIjtiOjA7fX1zOjI6ImpzIjtPOjM3OiJMYXJhdmVsXFJvc3RlclxFY29zeXN0ZW1zXEpzRWNvc3lzdGVtIjozOntzOjk6IgAqAGJ5TmFtZSI7YToxNDg6e3M6MjQ6IkBhbGNhbHpvbmUvYW5zaS10b2tlbml6ZSI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjI0OiJAYWxjYWx6b25lL2Fuc2ktdG9rZW5pemUiO3M6MTA6IgAqAHZlcnNpb24iO3M6NToiMC4zLjEiO3M6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cmFnb25cd3d3XGNvbWlkYVxub2RlX21vZHVsZXNcQGpyaWRnZXdlbGxcdHJhY2UtbWFwcGluZyI7fXM6MTg6IkBveGMtcHJvamVjdC90eXBlcyI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjE4OiJAb3hjLXByb2plY3QvdHlwZXMiO3M6MTA6IgAqAHZlcnNpb24iO3M6NzoiMC4xNTEuMCI7czo5OiIAKgBzb3VyY2UiO3I6MTA1NztzOjY6IgAqAGRldiI7YjoxO3M6OToiACoAZGlyZWN0IjtiOjA7czoxMzoiACoAY29uc3RyYWludCI7czowOiIiO3M6NzoiACoAcGF0aCI7czo1MzoiQzpcbGFyYWdvblx3d3dcY29taWRhXG5vZGVfbW9kdWxlc1xAb3hjLXByb2plY3RcdHlwZXMiO31zOjM0OiJAcm9sbGRvd24vYmluZGluZy1hbmRyb2lkLWFybS1lYWJpIjtPOjIyOiJMYXJhdmVsXFJvc3RlclxQYWNrYWdlIjo3OntzOjc6IgAqAG5hbWUiO3M6MzQ6IkByb2xsZG93bi9iaW5kaW5nLWFuZHJvaWQtYXJtLWVhYmkiO3M6MTA6IgAqAHZlcnNpb24iO3M6NjoiMS4yLjExIjtzOjk6IgAqAHNvdXJjZSI7cjoxMDU3O3M6NjoiACoAZGV2IjtiOjE7czo5OiIAKgBkaXJlY3QiO2I6MDtzOjEzOiIAKgBjb25zdHJhaW50IjtzOjA6IiI7czo3OiIAKgBwYXRoIjtzOjY5OiJDOlxsYXJhZ29uXHd3d1xjb21pZGFcbm9kZV9tb2R1bGVzXEByb2xsZG93blxiaW5kaW5nLWFuZHJvaWQtYXJtLWVhYmkiO31zOjMxOiJAcm9sbGRvd24vYmluZGluZy1hbmRyb2lkLWFybTY0IjtPOjIyOiJMYXJhdmVsXFJvc3RlclxQYWNrYWdlIjo3OntzOjc6IgAqAG5hbWUiO3M6MzE6IkByb2xsZG93bi9iaW5kaW5nLWFuZHJvaWQtYXJtNjQiO3M6MTA6IgAqAHZlcnNpb24iO3M6NjoiMS4yLjExIjtzOjk6IgAqAHNvdXJjZSI7cjoxMDU3O3M6NjoiACoAZGV2IjtiOjE7czo5OiIAKgBkaXJlY3QiO2I6MDtzOjEzOiIAKgBjb25zdHJhaW50IjtzOjA6IiI7czo3OiIAKgBwYXRoIjtzOjY2OiJDOlxsYXJhZ29uXHd3d1xjb21pZGFcbm9kZV9tb2R1bGVzXEByb2xsZG93blxiaW5kaW5nLWFuZHJvaWQtYXJtNjQiO31zOjMwOiJAcm9sbGRvd24vYmluZGluZy1kYXJ3aW4tYXJtNjQiO086MjI6IkxhcmF2ZWxcUm9zdGVyXFBhY2thZ2UiOjc6e3M6NzoiACoAbmFtZSI7czozMDoiQHJvbGxkb3duL2JpbmRpbmctZGFyd2luLWFybTY0IjtzOjEwOiIAKgB2ZXJzaW9uIjtzOjY6IjEuMi4xMSI7czo5OiIAKgBzb3VyY2UiO3I6MTA1NztzOjY6IgAqAGRldiI7YjoxO3M6OToiACoAZGlyZWN0IjtiOjA7czoxMzoiACoAY29uc3RyYWludCI7czowOiIiO3M6NzoiACoAcGF0aCI7czo2NToiQzpcbGFyYWdvblx3d3dcY29taWRhXG5vZGVfbW9kdWxlc1xAcm9sbGRvd25cYmluZGluZy1kYXJ3aW4tYXJtNjQiO31zOjI4OiJAcm9sbGRvd24vYmluZGluZy1kYXJ3aW4teDY0IjtPOjIyOiJMYXJhdmVsXFJvc3RlclxQYWNrYWdlIjo3OntzOjc6IgAqAG5hbWUiO3M6Mjg6IkByb2xsZG93bi9iaW5kaW5nLWRhcndpbi14NjQiO3M6MTA6IgAqAHZlcnNpb24iO3M6NjoiMS4yLjExIjtzOjk6IgAqAHNvdXJjZSI7cjoxMDU3O3M6NjoiACoAZGV2IjtiOjE7czo5OiIAKgBkaXJlY3QiO2I6MDtzOjEzOiIAKgBjb25zdHJhaW50IjtzOjA6IiI7czo3OiIAKgBwYXRoIjtzOjYzOiJDOlxsYXJhZ29uXHd3d1xjb21pZGFcbm9kZV9tb2R1bGVzXEByb2xsZG93blxiaW5kaW5nLWRhcndpbi14NjQiO31zOjI5OiJAcm9sbGRvd24vYmluZGluZy1mcmVlYnNkLXg2NCI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjI5OiJAcm9sbGRvd24vYmluZGluZy1mcmVlYnNkLXg2NCI7czoxMDoiACoAdmVyc2lvbiI7czo2OiIxLjIuMTEiO3M6OToiACoAc291cmNlIjtyOjEwNTc7czo2OiIAKgBkZXYiO2I6MTtzOjk6IgAqAGRpcmVjdCI7YjowO3M6MTM6IgAqAGNvbnN0cmFpbnQiO3M6MDoiIjtzOjc6IgAqAHBhdGgiO3M6NjQ6IkM6XGxhcmFnb25cd3d3XGNvbWlkYVxub2RlX21vZHVsZXNcQHJvbGxkb3duXGJpbmRpbmctZnJlZWJzZC14NjQiO31zOjM3OiJAcm9sbGRvd24vYmluZGluZy1saW51eC1hcm0tZ251ZWFiaWhmIjtPOjIyOiJMYXJhdmVsXFJvc3RlclxQYWNrYWdlIjo3OntzOjc6IgAqAG5hbWUiO3M6Mzc6IkByb2xsZG93bi9iaW5kaW5nLWxpbnV4LWFybS1nbnVlYWJpaGYiO3M6MTA6IgAqAHZlcnNpb24iO3M6NjoiMS4yLjExIjtzOjk6IgAqAHNvdXJjZSI7cjoxMDU3O3M6NjoiACoAZGV2IjtiOjE7czo5OiIAKgBkaXJlY3QiO2I6MDtzOjEzOiIAKgBjb25zdHJhaW50IjtzOjA6IiI7czo3OiIAKgBwYXRoIjtzOjcyOiJDOlxsYXJhZ29uXHd3d1xjb21pZGFcbm9kZV9tb2R1bGVzXEByb2xsZG93blxiaW5kaW5nLWxpbnV4LWFybS1nbnVlYWJpaGYiO31zOjMzOiJAcm9sbGRvd24vYmluZGluZy1saW51eC1hcm02NC1nbnUiO086MjI6IkxhcmF2ZWxcUm9zdGVyXFBhY2thZ2UiOjc6e3M6NzoiACoAbmFtZSI7czozMzoiQHJvbGxkb3duL2JpbmRpbmctbGludXgtYXJtNjQtZ251IjtzOjEwOiIAKgB2ZXJzaW9uIjtzOjY6IjEuMi4xMSI7czo5OiIAKgBzb3VyY2UiO3I6MTA1NztzOjY6IgAqAGRldiI7YjoxO3M6OToiACoAZGlyZWN0IjtiOjA7czoxMzoiACoAY29uc3RyYWludCI7czowOiIiO3M6NzoiACoAcGF0aCI7czo2ODoiQzpcbGFyYWdvblx3d3dcY29taWRhXG5vZGVfbW9kdWxlc1xAcm9sbGRvd25cYmluZGluZy1saW51eC1hcm02NC1nbnUiO31zOjM0OiJAcm9sbGRvd24vYmluZGluZy1saW51eC1hcm02NC1tdXNsIjtPOjIyOiJMYXJhdmVsXFJvc3RlclxQYWNrYWdlIjo3OntzOjc6IgAqAG5hbWUiO3M6MzQ6IkByb2xsZG93bi9iaW5kaW5nLWxpbnV4LWFybTY0LW11c2wiO3M6MTA6IgAqAHZlcnNpb24iO3M6NjoiMS4yLjExIjtzOjk6IgAqAHNvdXJjZSI7cjoxMDU3O3M6NjoiACoAZGV2IjtiOjE7czo5OiIAKgBkaXJlY3QiO2I6MDtzOjEzOiIAKgBjb25zdHJhaW50IjtzOjA6IiI7czo3OiIAKgBwYXRoIjtzOjY5OiJDOlxsYXJhZ29uXHd3d1xjb21pZGFcbm9kZV9tb2R1bGVzXEByb2xsZG93blxiaW5kaW5nLWxpbnV4LWFybTY0LW11c2wiO31zOjMzOiJAcm9sbGRvd24vYmluZGluZy1saW51eC1wcGM2NC1nbnUiO086MjI6IkxhcmF2ZWxcUm9zdGVyXFBhY2thZ2UiOjc6e3M6NzoiACoAbmFtZSI7czozMzoiQHJvbGxkb3duL2JpbmRpbmctbGludXgtcHBjNjQtZ251IjtzOjEwOiIAKgB2ZXJzaW9uIjtzOjY6IjEuMi4xMSI7czo5OiIAKgBzb3VyY2UiO3I6MTA1NztzOjY6IgAqAGRldiI7YjoxO3M6OToiACoAZGlyZWN0IjtiOjA7czoxMzoiACoAY29uc3RyYWludCI7czowOiIiO3M6NzoiACoAcGF0aCI7czo2ODoiQzpcbGFyYWdvblx3d3dcY29taWRhXG5vZGVfbW9kdWxlc1xAcm9sbGRvd25cYmluZGluZy1saW51eC1wcGM2NC1nbnUiO31zOjMzOiJAcm9sbGRvd24vYmluZGluZy1saW51eC1zMzkweC1nbnUiO086MjI6IkxhcmF2ZWxcUm9zdGVyXFBhY2thZ2UiOjc6e3M6NzoiACoAbmFtZSI7czozMzoiQHJvbGxkb3duL2JpbmRpbmctbGludXgtczM5MHgtZ251IjtzOjEwOiIAKgB2ZXJzaW9uIjtzOjY6IjEuMi4xMSI7czo5OiIAKgBzb3VyY2UiO3I6MTA1NztzOjY6IgAqAGRldiI7YjoxO3M6OToiACoAZGlyZWN0IjtiOjA7czoxMzoiACoAY29uc3RyYWludCI7czowOiIiO3M6NzoiACoAcGF0aCI7czo2ODoiQzpcbGFyYWdvblx3d3dcY29taWRhXG5vZGVfbW9kdWxlc1xAcm9sbGRvd25cYmluZGluZy1saW51eC1zMzkweC1nbnUiO31zOjMxOiJAcm9sbGRvd24vYmluZGluZy1saW51eC14NjQtZ251IjtPOjIyOiJMYXJhdmVsXFJvc3RlclxQYWNrYWdlIjo3OntzOjc6IgAqAG5hbWUiO3M6MzE6IkByb2xsZG93bi9iaW5kaW5nLWxpbnV4LXg2NC1nbnUiO3M6MTA6IgAqAHZlcnNpb24iO3M6NjoiMS4yLjExIjtzOjk6IgAqAHNvdXJjZSI7cjoxMDU3O3M6NjoiACoAZGV2IjtiOjE7czo5OiIAKgBkaXJlY3QiO2I6MDtzOjEzOiIAKgBjb25zdHJhaW50IjtzOjA6IiI7czo3OiIAKgBwYXRoIjtzOjY2OiJDOlxsYXJhZ29uXHd3d1xjb21pZGFcbm9kZV9tb2R1bGVzXEByb2xsZG93blxiaW5kaW5nLWxpbnV4LXg2NC1nbnUiO31zOjMyOiJAcm9sbGRvd24vYmluZGluZy1saW51eC14NjQtbXVzbCI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjMyOiJAcm9sbGRvd24vYmluZGluZy1saW51eC14NjQtbXVzbCI7czoxMDoiACoAdmVyc2lvbiI7czo2OiIxLjIuMTEiO3M6OToiACoAc291cmNlIjtyOjEwNTc7czo2OiIAKgBkZXYiO2I6MTtzOjk6IgAqAGRpcmVjdCI7YjowO3M6MTM6IgAqAGNvbnN0cmFpbnQiO3M6MDoiIjtzOjc6IgAqAHBhdGgiO3M6Njc6IkM6XGxhcmFnb25cd3d3XGNvbWlkYVxub2RlX21vZHVsZXNcQHJvbGxkb3duXGJpbmRpbmctbGludXgteDY0LW11c2wiO31zOjM1OiJAcm9sbGRvd24vYmluZGluZy1vcGVuaGFybW9ueS1hcm02NCI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjM1OiJAcm9sbGRvd24vYmluZGluZy1vcGVuaGFybW9ueS1hcm02NCI7czoxMDoiACoAdmVyc2lvbiI7czo2OiIxLjIuMTEiO3M6OToiACoAc291cmNlIjtyOjEwNTc7czo2OiIAKgBkZXYiO2I6MTtzOjk6IgAqAGRpcmVjdCI7YjowO3M6MTM6IgAqAGNvbnN0cmFpbnQiO3M6MDoiIjtzOjc6IgAqAHBhdGgiO3M6NzA6IkM6XGxhcmFnb25cd3d3XGNvbWlkYVxub2RlX21vZHVsZXNcQHJvbGxkb3duXGJpbmRpbmctb3Blbmhhcm1vbnktYXJtNjQiO31zOjM0OiJAcm9sbGRvd24vYmluZGluZy13aW4zMi1hcm02NC1tc3ZjIjtPOjIyOiJMYXJhdmVsXFJvc3RlclxQYWNrYWdlIjo3OntzOjc6IgAqAG5hbWUiO3M6MzQ6IkByb2xsZG93bi9iaW5kaW5nLXdpbjMyLWFybTY0LW1zdmMiO3M6MTA6IgAqAHZlcnNpb24iO3M6NjoiMS4yLjExIjtzOjk6IgAqAHNvdXJjZSI7cjoxMDU3O3M6NjoiACoAZGV2IjtiOjE7czo5OiIAKgBkaXJlY3QiO2I6MDtzOjEzOiIAKgBjb25zdHJhaW50IjtzOjA6IiI7czo3OiIAKgBwYXRoIjtzOjY5OiJDOlxsYXJhZ29uXHd3d1xjb21pZGFcbm9kZV9tb2R1bGVzXEByb2xsZG93blxiaW5kaW5nLXdpbjMyLWFybTY0LW1zdmMiO31zOjMyOiJAcm9sbGRvd24vYmluZGluZy13aW4zMi14NjQtbXN2YyI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjMyOiJAcm9sbGRvd24vYmluZGluZy13aW4zMi14NjQtbXN2YyI7czoxMDoiACoAdmVyc2lvbiI7czo2OiIxLjIuMTEiO3M6OToiACoAc291cmNlIjtyOjEwNTc7czo2OiIAKgBkZXYiO2I6MTtzOjk6IgAqAGRpcmVjdCI7YjowO3M6MTM6IgAqAGNvbnN0cmFpbnQiO3M6MDoiIjtzOjc6IgAqAHBhdGgiO3M6Njc6IkM6XGxhcmFnb25cd3d3XGNvbWlkYVxub2RlX21vZHVsZXNcQHJvbGxkb3duXGJpbmRpbmctd2luMzIteDY0LW1zdmMiO31zOjIxOiJAcm9sbGRvd24vcGx1Z2ludXRpbHMiO086MjI6IkxhcmF2ZWxcUm9zdGVyXFBhY2thZ2UiOjc6e3M6NzoiACoAbmFtZSI7czoyMToiQHJvbGxkb3duL3BsdWdpbnV0aWxzIjtzOjEwOiIAKgB2ZXJzaW9uIjtzOjU6IjEuMC4xIjtzOjk6IgAqAHNvdXJjZSI7cjoxMDU3O3M6NjoiACoAZGV2IjtiOjE7czo5OiIAKgBkaXJlY3QiO2I6MDtzOjEzOiIAKgBjb25zdHJhaW50IjtzOjA6IiI7czo3OiIAKgBwYXRoIjtzOjU2OiJDOlxsYXJhZ29uXHd3d1xjb21pZGFcbm9kZV9tb2R1bGVzXEByb2xsZG93blxwbHVnaW51dGlscyI7fXM6MTc6IkB0YWlsd2luZGNzcy9ub2RlIjtPOjIyOiJMYXJhdmVsXFJvc3RlclxQYWNrYWdlIjo3OntzOjc6IgAqAG5hbWUiO3M6MTc6IkB0YWlsd2luZGNzcy9ub2RlIjtzOjEwOiIAKgB2ZXJzaW9uIjtzOjU6IjQuMy4zIjtzOjk6IgAqAHNvdXJjZSI7cjoxMDU3O3M6NjoiACoAZGV2IjtiOjE7czo5OiIAKgBkaXJlY3QiO2I6MDtzOjEzOiIAKgBjb25zdHJhaW50IjtzOjA6IiI7czo3OiIAKgBwYXRoIjtzOjUyOiJDOlxsYXJhZ29uXHd3d1xjb21pZGFcbm9kZV9tb2R1bGVzXEB0YWlsd2luZGNzc1xub2RlIjt9czoxODoiQHRhaWx3aW5kY3NzL294aWRlIjtPOjIyOiJMYXJhdmVsXFJvc3RlclxQYWNrYWdlIjo3OntzOjc6IgAqAG5hbWUiO3M6MTg6IkB0YWlsd2luZGNzcy9veGlkZSI7czoxMDoiACoAdmVyc2lvbiI7czo1OiI0LjMuMyI7czo5OiIAKgBzb3VyY2UiO3I6MTA1NztzOjY6IgAqAGRldiI7YjoxO3M6OToiACoAZGlyZWN0IjtiOjA7czoxMzoiACoAY29uc3RyYWludCI7czowOiIiO3M6NzoiACoAcGF0aCI7czo1MzoiQzpcbGFyYWdvblx3d3dcY29taWRhXG5vZGVfbW9kdWxlc1xAdGFpbHdpbmRjc3Ncb3hpZGUiO31zOjMyOiJAdGFpbHdpbmRjc3Mvb3hpZGUtYW5kcm9pZC1hcm02NCI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjMyOiJAdGFpbHdpbmRjc3Mvb3hpZGUtYW5kcm9pZC1hcm02NCI7czoxMDoiACoAdmVyc2lvbiI7czo1OiI0LjMuMyI7czo5OiIAKgBzb3VyY2UiO3I6MTA1NztzOjY6IgAqAGRldiI7YjoxO3M6OToiACoAZGlyZWN0IjtiOjA7czoxMzoiACoAY29uc3RyYWludCI7czowOiIiO3M6NzoiACoAcGF0aCI7czo2NzoiQzpcbGFyYWdvblx3d3dcY29taWRhXG5vZGVfbW9kdWxlc1xAdGFpbHdpbmRjc3Ncb3hpZGUtYW5kcm9pZC1hcm02NCI7fXM6MzE6IkB0YWlsd2luZGNzcy9veGlkZS1kYXJ3aW4tYXJtNjQiO086MjI6IkxhcmF2ZWxcUm9zdGVyXFBhY2thZ2UiOjc6e3M6NzoiACoAbmFtZSI7czozMToiQHRhaWx3aW5kY3NzL294aWRlLWRhcndpbi1hcm02NCI7czoxMDoiACoAdmVyc2lvbiI7czo1OiI0LjMuMyI7czo5OiIAKgBzb3VyY2UiO3I6MTA1NztzOjY6IgAqAGRldiI7YjoxO3M6OToiACoAZGlyZWN0IjtiOjA7czoxMzoiACoAY29uc3RyYWludCI7czowOiIiO3M6NzoiACoAcGF0aCI7czo2NjoiQzpcbGFyYWdvblx3d3dcY29taWRhXG5vZGVfbW9kdWxlc1xAdGFpbHdpbmRjc3Ncb3hpZGUtZGFyd2luLWFybTY0Ijt9czoyOToiQHRhaWx3aW5kY3NzL294aWRlLWRhcndpbi14NjQiO086MjI6IkxhcmF2ZWxcUm9zdGVyXFBhY2thZ2UiOjc6e3M6NzoiACoAbmFtZSI7czoyOToiQHRhaWx3aW5kY3NzL294aWRlLWRhcndpbi14NjQiO3M6MTA6IgAqAHZlcnNpb24iO3M6NToiNC4zLjMiO3M6OToiACoAc291cmNlIjtyOjEwNTc7czo2OiIAKgBkZXYiO2I6MTtzOjk6IgAqAGRpcmVjdCI7YjowO3M6MTM6IgAqAGNvbnN0cmFpbnQiO3M6MDoiIjtzOjc6IgAqAHBhdGgiO3M6NjQ6IkM6XGxhcmFnb25cd3d3XGNvbWlkYVxub2RlX21vZHVsZXNcQHRhaWx3aW5kY3NzXG94aWRlLWRhcndpbi14NjQiO31zOjMwOiJAdGFpbHdpbmRjc3Mvb3hpZGUtZnJlZWJzZC14NjQiO086MjI6IkxhcmF2ZWxcUm9zdGVyXFBhY2thZ2UiOjc6e3M6NzoiACoAbmFtZSI7czozMDoiQHRhaWx3aW5kY3NzL294aWRlLWZyZWVic2QteDY0IjtzOjEwOiIAKgB2ZXJzaW9uIjtzOjU6IjQuMy4zIjtzOjk6IgAqAHNvdXJjZSI7cjoxMDU3O3M6NjoiACoAZGV2IjtiOjE7czo5OiIAKgBkaXJlY3QiO2I6MDtzOjEzOiIAKgBjb25zdHJhaW50IjtzOjA6IiI7czo3OiIAKgBwYXRoIjtzOjY1OiJDOlxsYXJhZ29uXHd3d1xjb21pZGFcbm9kZV9tb2R1bGVzXEB0YWlsd2luZGNzc1xveGlkZS1mcmVlYnNkLXg2NCI7fXM6Mzg6IkB0YWlsd2luZGNzcy9veGlkZS1saW51eC1hcm0tZ251ZWFiaWhmIjtPOjIyOiJMYXJhdmVsXFJvc3RlclxQYWNrYWdlIjo3OntzOjc6IgAqAG5hbWUiO3M6Mzg6IkB0YWlsd2luZGNzcy9veGlkZS1saW51eC1hcm0tZ251ZWFiaWhmIjtzOjEwOiIAKgB2ZXJzaW9uIjtzOjU6IjQuMy4zIjtzOjk6IgAqAHNvdXJjZSI7cjoxMDU3O3M6NjoiACoAZGV2IjtiOjE7czo5OiIAKgBkaXJlY3QiO2I6MDtzOjEzOiIAKgBjb25zdHJhaW50IjtzOjA6IiI7czo3OiIAKgBwYXRoIjtzOjczOiJDOlxsYXJhZ29uXHd3d1xjb21pZGFcbm9kZV9tb2R1bGVzXEB0YWlsd2luZGNzc1xveGlkZS1saW51eC1hcm0tZ251ZWFiaWhmIjt9czozNDoiQHRhaWx3aW5kY3NzL294aWRlLWxpbnV4LWFybTY0LWdudSI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjM0OiJAdGFpbHdpbmRjc3Mvb3hpZGUtbGludXgtYXJtNjQtZ251IjtzOjEwOiIAKgB2ZXJzaW9uIjtzOjU6IjQuMy4zIjtzOjk6IgAqAHNvdXJjZSI7cjoxMDU3O3M6NjoiACoAZGV2IjtiOjE7czo5OiIAKgBkaXJlY3QiO2I6MDtzOjEzOiIAKgBjb25zdHJhaW50IjtzOjA6IiI7czo3OiIAKgBwYXRoIjtzOjY5OiJDOlxsYXJhZ29uXHd3d1xjb21pZGFcbm9kZV9tb2R1bGVzXEB0YWlsd2luZGNzc1xveGlkZS1saW51eC1hcm02NC1nbnUiO31zOjM1OiJAdGFpbHdpbmRjc3Mvb3hpZGUtbGludXgtYXJtNjQtbXVzbCI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjM1OiJAdGFpbHdpbmRjc3Mvb3hpZGUtbGludXgtYXJtNjQtbXVzbCI7czoxMDoiACoAdmVyc2lvbiI7czo1OiI0LjMuMyI7czo5OiIAKgBzb3VyY2UiO3I6MTA1NztzOjY6IgAqAGRldiI7YjoxO3M6OToiACoAZGlyZWN0IjtiOjA7czoxMzoiACoAY29uc3RyYWludCI7czowOiIiO3M6NzoiACoAcGF0aCI7czo3MDoiQzpcbGFyYWdvblx3d3dcY29taWRhXG5vZGVfbW9kdWxlc1xAdGFpbHdpbmRjc3Ncb3hpZGUtbGludXgtYXJtNjQtbXVzbCI7fXM6MzI6IkB0YWlsd2luZGNzcy9veGlkZS1saW51eC14NjQtZ251IjtPOjIyOiJMYXJhdmVsXFJvc3RlclxQYWNrYWdlIjo3OntzOjc6IgAqAG5hbWUiO3M6MzI6IkB0YWlsd2luZGNzcy9veGlkZS1saW51eC14NjQtZ251IjtzOjEwOiIAKgB2ZXJzaW9uIjtzOjU6IjQuMy4zIjtzOjk6IgAqAHNvdXJjZSI7cjoxMDU3O3M6NjoiACoAZGV2IjtiOjE7czo5OiIAKgBkaXJlY3QiO2I6MDtzOjEzOiIAKgBjb25zdHJhaW50IjtzOjA6IiI7czo3OiIAKgBwYXRoIjtzOjY3OiJDOlxsYXJhZ29uXHd3d1xjb21pZGFcbm9kZV9tb2R1bGVzXEB0YWlsd2luZGNzc1xveGlkZS1saW51eC14NjQtZ251Ijt9czozMzoiQHRhaWx3aW5kY3NzL294aWRlLWxpbnV4LXg2NC1tdXNsIjtPOjIyOiJMYXJhdmVsXFJvc3RlclxQYWNrYWdlIjo3OntzOjc6IgAqAG5hbWUiO3M6MzM6IkB0YWlsd2luZGNzcy9veGlkZS1saW51eC14NjQtbXVzbCI7czoxMDoiACoAdmVyc2lvbiI7czo1OiI0LjMuMyI7czo5OiIAKgBzb3VyY2UiO3I6MTA1NztzOjY6IgAqAGRldiI7YjoxO3M6OToiACoAZGlyZWN0IjtiOjA7czoxMzoiACoAY29uc3RyYWludCI7czowOiIiO3M6NzoiACoAcGF0aCI7czo2ODoiQzpcbGFyYWdvblx3d3dcY29taWRhXG5vZGVfbW9kdWxlc1xAdGFpbHdpbmRjc3Ncb3hpZGUtbGludXgteDY0LW11c2wiO31zOjMwOiJAdGFpbHdpbmRjc3Mvb3hpZGUtd2FzbTMyLXdhc2kiO086MjI6IkxhcmF2ZWxcUm9zdGVyXFBhY2thZ2UiOjc6e3M6NzoiACoAbmFtZSI7czozMDoiQHRhaWx3aW5kY3NzL294aWRlLXdhc20zMi13YXNpIjtzOjEwOiIAKgB2ZXJzaW9uIjtzOjU6IjQuMy4zIjtzOjk6IgAqAHNvdXJjZSI7cjoxMDU3O3M6NjoiACoAZGV2IjtiOjE7czo5OiIAKgBkaXJlY3QiO2I6MDtzOjEzOiIAKgBjb25zdHJhaW50IjtzOjA6IiI7czo3OiIAKgBwYXRoIjtzOjY1OiJDOlxsYXJhZ29uXHd3d1xjb21pZGFcbm9kZV9tb2R1bGVzXEB0YWlsd2luZGNzc1xveGlkZS13YXNtMzItd2FzaSI7fXM6MzU6IkB0YWlsd2luZGNzcy9veGlkZS13aW4zMi1hcm02NC1tc3ZjIjtPOjIyOiJMYXJhdmVsXFJvc3RlclxQYWNrYWdlIjo3OntzOjc6IgAqAG5hbWUiO3M6MzU6IkB0YWlsd2luZGNzcy9veGlkZS13aW4zMi1hcm02NC1tc3ZjIjtzOjEwOiIAKgB2ZXJzaW9uIjtzOjU6IjQuMy4zIjtzOjk6IgAqAHNvdXJjZSI7cjoxMDU3O3M6NjoiACoAZGV2IjtiOjE7czo5OiIAKgBkaXJlY3QiO2I6MDtzOjEzOiIAKgBjb25zdHJhaW50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O3M6MTM6IgAqAGNvbnN0cmFpbnQiO3M6MDoiIjtzOjc6IgAqAHBhdGgiO3M6NTk6IkM6XGxhcmFnb25cd3d3XGNvbWlkYVxub2RlX21vZHVsZXNcbGlnaHRuaW5nY3NzLWZyZWVic2QteDY0Ijt9czozMjoibGlnaHRuaW5nY3NzLWxpbnV4LWFybS1nbnVlYWJpaGYiO086MjI6IkxhcmF2ZWxcUm9zdGVyXFBhY2thZ2UiOjc6e3M6NzoiACoAbmFtZSI7czozMjoibGlnaHRuaW5nY3NzLWxpbnV4LWFybS1nbnVlYWJpaGYiO3M6MTA6IgAqAHZlcnNpb24iO3M6NjoiMS4zMi4wIjtzOjk6IgAqAHNvdXJjZSI7cjoxMDU3O3M6NjoiACoAZGV2IjtiOjE7czo5OiIAKgBkaXJlY3QiO2I6MDtzOjEzOiIAKgBjb25zdHJhaW50IjtzOjA6IiI7czo3OiIAKgBwYXRoIjtzOjY3OiJDOlxsYXJhZ29uXHd3d1xjb21pZGFcbm9kZV9tb2R1bGVzXGxpZ2h0bmluZ2Nzcy1saW51eC1hcm0tZ251ZWFiaWhmIjt9czoyODoibGlnaHRuaW5nY3NzLWxpbnV4LWFybTY0LWdudSI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjI4OiJsaWdodG5pbmdjc3MtbGludXgtYXJtNjQtZ251IjtzOjEwOiIAKgB2ZXJzaW9uIjtzOjY6IjEuMzIuMCI7czo5OiIAKgBzb3VyY2UiO3I6MTA1NztzOjY6IgAqAGRldiI7YjoxO3M6OToiACoAZGlyZWN0IjtiOjA7czoxMzoiACoAY29uc3RyYWludCI7czowOiIiO3M6NzoiACoAcGF0aCI7czo2MzoiQzpcbGFyYWdvblx3d3dcY29taWRhXG5vZGVfbW9kdWxlc1xsaWdodG5pbmdjc3MtbGludXgtYXJtNjQtZ251Ijt9czoyOToibGlnaHRuaW5nY3NzLWxpbnV4LWFybTY0LW11c2wiO086MjI6IkxhcmF2ZWxcUm9zdGVyXFBhY2thZ2UiOjc6e3M6NzoiACoAbmFtZSI7czoyOToibGlnaHRuaW5nY3NzLWxpbnV4LWFybTY0LW11c2wiO3M6MTA6IgAqAHZlcnNpb24iO3M6NjoiMS4zMi4wIjtzOjk6IgAqAHNvdXJjZSI7cjoxMDU3O3M6NjoiACoAZGV2IjtiOjE7czo5OiIAKgBkaXJlY3QiO2I6MDtzOjEzOiIAKgBjb25zdHJhaW50IjtzOjA6IiI7czo3OiIAKgBwYXRoIjtzOjY0OiJDOlxsYXJhZ29uXHd3d1xjb21pZGFcbm9kZV9tb2R1bGVzXGxpZ2h0bmluZ2Nzcy1saW51eC1hcm02NC1tdXNsIjt9czoyNjoibGlnaHRuaW5nY3NzLWxpbnV4LXg2NC1nbnUiO086MjI6IkxhcmF2ZWxcUm9zdGVyXFBhY2thZ2UiOjc6e3M6NzoiACoAbmFtZSI7czoyNjoibGlnaHRuaW5nY3NzLWxpbnV4LXg2NC1nbnUiO3M6MTA6IgAqAHZlcnNpb24iO3M6NjoiMS4zMi4wIjtzOjk6IgAqAHNvdXJjZSI7cjoxMDU3O3M6NjoiACoAZGV2IjtiOjE7czo5OiIAKgBkaXJlY3QiO2I6MDtzOjEzOiIAKgBjb25zdHJhaW50IjtzOjA6IiI7czo3OiIAKgBwYXRoIjtzOjYxOiJDOlxsYXJhZ29uXHd3d1xjb21pZGFcbm9kZV9tb2R1bGVzXGxpZ2h0bmluZ2Nzcy1saW51eC14NjQtZ251Ijt9czoyNzoibGlnaHRuaW5nY3NzLWxpbnV4LXg2NC1tdXNsIjtPOjIyOiJMYXJhdmVsXFJvc3RlclxQYWNrYWdlIjo3OntzOjc6IgAqAG5hbWUiO3M6Mjc6ImxpZ2h0bmluZ2Nzcy1saW51eC14NjQtbXVzbCI7czoxMDoiACoAdmVyc2lvbiI7czo2OiIxLjMyLjAiO3M6OToiACoAc291cmNlIjtyOjEwNTc7czo2OiIAKgBkZXYiO2I6MTtzOjk6IgAqAGRpcmVjdCI7YjowO3M6MTM6IgAqAGNvbnN0cmFpbnQiO3M6MDoiIjtzOjc6IgAqAHBhdGgiO3M6NjI6IkM6XGxhcmFnb25cd3d3XGNvbWlkYVxub2RlX21vZHVsZXNcbGlnaHRuaW5nY3NzLWxpbnV4LXg2NC1tdXNsIjt9czoyOToibGlnaHRuaW5nY3NzLXdpbjMyLWFybTY0LW1zdmMiO086MjI6IkxhcmF2ZWxcUm9zdGVyXFBhY2th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NTQ7aToyNjtyOjEyNjI7aToyNztyOjEyNzA7aToyODtyOjEyNzg7aToyOTtyOjEyODY7aTozMDtyOjEyOTQ7aTozMTtyOjEzMDI7aTozMjtyOjEzMTA7aTozMztyOjEzMTg7aTozNDtyOjEzMjY7aTozNTtyOjEzMzQ7aTozNjtyOjEzNDI7aTozNztyOjEzNTA7aTozODtyOjEzNTg7aTozOTtyOjEzNjY7aTo0MDtyOjEzNzQ7aTo0MTtyOjEzODI7aTo0MjtyOjEzOTA7aTo0MztyOjEzOTg7aTo0NDtyOjE0MDY7aTo0NTtyOjE0MTQ7aTo0NjtyOjE0MjI7aTo0NztyOjE0MzA7aTo0ODtyOjE0Mzg7aTo0OTtyOjE0NDY7aTo1MDtyOjE0NTQ7aTo1MTtyOjE0NjI7aTo1MjtyOjE0NzA7aTo1MztyOjE0Nzg7aTo1NDtyOjE0ODY7aTo1NTtyOjE0OTQ7aTo1NjtyOjE1MDI7aTo1NztyOjE1MTA7aTo1ODtyOjE1MTg7aTo1OTtyOjE1MjY7aTo2MDtyOjE1MzQ7aTo2MTtyOjE1NDI7aTo2MjtyOjE1NTA7aTo2MztyOjE1NTg7aTo2NDtyOjE1NjY7aTo2NTtyOjE1NzQ7aTo2NjtyOjE1ODI7aTo2NztyOjE1OTA7aTo2ODtyOjE1OTg7aTo2OTtyOjE2MDY7aTo3MDtyOjE2MTQ7aTo3MTtyOjE2MjI7aTo3MjtyOjE2MzA7aTo3MztyOjE2Mzg7aTo3NDtyOjE2NDY7aTo3NTtyOjE2NTQ7aTo3NjtyOjE2NjI7aTo3NztyOjE2NzA7aTo3ODtyOjE2Nzg7aTo3OTtyOjE2ODY7aTo4MDtyOjE2OTQ7aTo4MTtyOjE3MDI7aTo4MjtyOjE3MTA7aTo4MztyOjE3MTg7aTo4NDtyOjE3MjY7aTo4NTtyOjE3MzQ7aTo4NjtyOjE3NDI7aTo4NztyOjE3NTA7aTo4ODtyOjE3NTg7aTo4OTtyOjE3NjY7aTo5MDtyOjE3NzQ7aTo5MTtyOjE3ODI7aTo5MjtyOjE3OTA7aTo5MztyOjE3OTg7aTo5NDtyOjE4MDY7aTo5NTtyOjE4MTQ7aTo5NjtyOjE4MjI7aTo5NztyOjE4MzA7aTo5ODtyOjE4Mzg7aTo5OTtyOjE4NDY7aToxMDA7cjoxODU0O2k6MTAxO3I6MTg2MjtpOjEwMjtyOjE4NzA7aToxMDM7cjoxODc4O2k6MTA0O3I6MTg4NjtpOjEwNTtyOjE4OTQ7aToxMDY7cjoxOTAyO2k6MTA3O3I6MTkxMDtpOjEwODtyOjE5MTg7aToxMDk7cjoxOTI2O2k6MTEwO3I6MTkzNDtpOjExMTtyOjE5NDI7aToxMTI7cjoxOTUwO2k6MTEzO3I6MTk1ODtpOjExNDtyOjE5NjY7aToxMTU7cjoxOTc0O2k6MTE2O3I6MTk4MjtpOjExNztyOjE5OTA7aToxMTg7cjoxOTk4O2k6MTE5O3I6MjAwNjtpOjEyMDtyOjIwMTQ7aToxMjE7cjoyMDIyO2k6MTIyO3I6MjAzMDtpOjEyMztyOjIwMzg7aToxMjQ7cjoyMDQ2O2k6MTI1O3I6MjA1NDtpOjEyNjtyOjIwNjI7aToxMjc7cjoyMDcwO2k6MTI4O3I6MjA3ODtpOjEyOTtyOjIwODY7aToxMzA7cjoyMDk0O2k6MTMxO3I6MjEwMjtpOjEzMjtyOjIxMTA7aToxMzM7cjoyMTE4O2k6MTM0O3I6MjEyNjtpOjEzNTtyOjIxMzQ7aToxMzY7cjoyMTQyO2k6MTM3O3I6MjE1MDtpOjEzODtyOjIxNTg7aToxMzk7cjoyMTY2O2k6MTQwO3I6MjE3NDtpOjE0MTtyOjIxODI7aToxNDI7cjoyMTkwO2k6MTQzO3I6MjE5ODtpOjE0NDtyOjIyMDY7aToxNDU7cjoyMjE0O2k6MTQ2O3I6MjIyMjtpOjE0NztyOjIyMzA7fXM6Mjg6IgAqAGVzY2FwZVdoZW5DYXN0aW5nVG9TdHJpbmciO2I6MDt9czoxNzoiACoAcGFja2FnZU1hbmFnZXIiO0U6NDE6IkxhcmF2ZWxcUm9zdGVyXEVudW1zXEpzUGFja2FnZU1hbmFnZXI6TnBtIjt9czo2OiJzdGFja3MiO086MzA6IkxhcmF2ZWxcUm9zdGVyXFN1cHBvcnRcRW51bVNldCI6MTp7czo4OiIAKgBjYXNlcyI7YToxOntpOjA7RTozMDoiTGFyYXZlbFxSb3N0ZXJcRW51bXNcU3RhY2s6QXBpIjt9fXM6MjE6ImJyb3dzZXJUZXN0RnJhbWV3b3JrcyI7TzozMDoiTGFyYXZlbFxSb3N0ZXJcU3VwcG9ydFxFbnVtU2V0IjoxOntzOjg6IgAqAGNhc2VzIjthOjA6e319czo5OiJmcm9udGVuZHMiO086MzA6IkxhcmF2ZWxcUm9zdGVyXFN1cHBvcnRcRW51bVNldCI6MTp7czo4OiIAKgBjYXNlcyI7YTowOnt9fXM6NjoiYWdlbnRzIjtPOjMwOiJMYXJhdmVsXFJvc3RlclxTdXBwb3J0XEVudW1TZXQiOjE6e3M6ODoiACoAY2FzZXMiO2E6Mjp7aTowO0U6Mzc6IkxhcmF2ZWxcUm9zdGVyXEVudW1zXEFnZW50OkNsYXVkZUNvZGUiO2k6MTtFOjMyOiJMYXJhdmVsXFJvc3RlclxFbnVtc1xBZ2VudDpDb2RleCI7fX1zOjc6ImVkaXRvcnMiO086MzA6IkxhcmF2ZWxcUm9zdGVyXFN1cHBvcnRcRW51bVNldCI6MTp7czo4OiIAKgBjYXNlcyI7YTowOnt9fX0=	1791771534
sazon-cache-roster:project:v3:e56f76a8c26d3841db8793abcc91d857	TzoyNjoiTGFyYXZlbFxSb3N0ZXJcUHJvamVjdFNjYW4iOjg6e3M6ODoiYmFzZVBhdGgiO3M6MjI6IkM6XGxhcmFnb25cd3d3XGNvbWlkYVwiO3M6MzoicGhwIjtPOjM1OiJMYXJhdmVsXFJvc3RlclxFY29zeXN0ZW1zXEVjb3N5c3RlbSI6Mjp7czo5OiIAKgBieU5hbWUiO2E6MTE2OntzOjEwOiJicmljay9tYXRoIjtPOjIyOiJMYXJhdmVsXFJvc3RlclxQYWNrYWdlIjo3OntzOjc6IgAqAG5hbWUiO3M6MTA6ImJyaWNrL21hdGgiO3M6MTA6IgAqAHZlcnNpb24iO3M6NToiMS4wLjAiO3M6OToiACoAc291cmNlIjtFOjQzOiJMYXJhdmVsXFJvc3RlclxFbnVtc1xQYWNrYWdlU291cmNlOkNvbXBvc2VyIjtzOjY6IgAqAGRldiI7YjowO3M6OToiACoAZGlyZWN0IjtiOjA7czoxMzoiACoAY29uc3RyYWludCI7czowOiIiO3M6NzoiACoAcGF0aCI7czozOToiQzpcbGFyYWdvblx3d3dcY29taWRhXHZlbmRvclxicmlja1xtYXRoIjt9czozMToiY2FyYm9ucGhwL2NhcmJvbi1kb2N0cmluZS10eXBlcyI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjMxOiJjYXJib25waHAvY2FyYm9uLWRvY3RyaW5lLXR5cGVzIjtzOjEwOiIAKgB2ZXJzaW9uIjtzOjU6IjMuMi4xIjtzOjk6IgAqAHNvdXJjZSI7cjo4O3M6NjoiACoAZGV2IjtiOjA7czo5OiIAKgBkaXJlY3QiO2I6MDtzOjEzOiIAKgBjb25zdHJhaW50IjtzOjA6IiI7czo3OiIAKgBwYXRoIjtzOjYwOiJDOlxsYXJhZ29uXHd3d1xjb21pZGFcdmVuZG9yXGNhcmJvbnBocFxjYXJib24tZG9jdHJpbmUtdHlwZXMiO31zOjIzOiJkZmx5ZGV2L2RvdC1hY2Nlc3MtZGF0YSI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjIzOiJkZmx5ZGV2L2RvdC1hY2Nlc3MtZGF0YSI7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cG9seWZpbGwtaW50bC1ncmFwaGVtZSI7czoxMDoiACoAdmVyc2lvbiI7czo2OiIxLjQxLjAiO3M6OToiACoAc291cmNlIjtyOjg7czo2OiIAKgBkZXYiO2I6MDtzOjk6IgAqAGRpcmVjdCI7YjowO3M6MTM6IgAqAGNvbnN0cmFpbnQiO3M6MDoiIjtzOjc6IgAqAHBhdGgiO3M6NTk6IkM6XGxhcmFnb25cd3d3XGNvbWlkYVx2ZW5kb3Jcc3ltZm9ueVxwb2x5ZmlsbC1pbnRsLWdyYXBoZW1lIjt9czoyNToic3ltZm9ueS9wb2x5ZmlsbC1pbnRsLWlkbiI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjI1OiJzeW1mb255L3BvbHlmaWxsLWludGwtaWRuIjtzOjEwOiIAKgB2ZXJzaW9uIjtzOjY6IjEuNDIuMCI7czo5OiIAKgBzb3VyY2UiO3I6ODtzOjY6IgAqAGRldiI7YjowO3M6OToiACoAZGlyZWN0IjtiOjA7czoxMzoiACoAY29uc3RyYWludCI7czowOiIiO3M6NzoiACoAcGF0aCI7czo1NDoiQzpcbGFyYWdvblx3d3dcY29taWRhXHZlbmRvclxzeW1mb255XHBvbHlmaWxsLWludGwtaWRuIjt9czozMjoic3ltZm9ueS9wb2x5ZmlsbC1pbnRsLW5vcm1hbGl6ZXIiO086MjI6IkxhcmF2ZWxcUm9zdGVyXFBhY2thZ2UiOjc6e3M6NzoiACoAbmFtZSI7czozMjoic3ltZm9ueS9wb2x5ZmlsbC1pbnRsLW5vcm1hbGl6ZXIiO3M6MTA6IgAqAHZlcnNpb24iO3M6NjoiMS40Mi4wIjtzOjk6IgAqAHNvdXJjZSI7cjo4O3M6NjoiACoAZGV2IjtiOjA7czo5OiIAKgBkaXJlY3QiO2I6MDtzOjEzOiIAKgBjb25zdHJhaW50IjtzOjA6IiI7czo3OiIAKgBwYXRoIjtzOjYxOiJDOlxsYXJhZ29uXHd3d1xjb21pZGFcdmVuZG9yXHN5bWZvbnlccG9seWZpbGwtaW50bC1ub3JtYWxpemVyIjt9czoyNToic3ltZm9ueS9wb2x5ZmlsbC1tYnN0cmluZyI7TzoyMjoiTGFyYXZl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NztpOjYwO3I6NDg1O2k6NjE7cjo0OTM7aTo2MjtyOjUwMTtpOjYzO3I6NTA5O2k6NjQ7cjo1MTc7aTo2NTtyOjUyNTtpOjY2O3I6NTMzO2k6Njc7cjo1NDE7aTo2ODtyOjU0OTtpOjY5O3I6NTU3O2k6NzA7cjo1NjU7aTo3MTtyOjU3MztpOjcyO3I6NTgxO2k6NzM7cjo1ODk7aTo3NDtyOjU5NztpOjc1O3I6NjA1O2k6NzY7cjo2MTM7aTo3NztyOjYyMTtpOjc4O3I6NjI5O2k6Nzk7cjo2Mzc7aTo4MDtyOjY0NTtpOjgxO3I6NjUzO2k6ODI7cjo2NjE7aTo4MztyOjY2OTtpOjg0O3I6Njc3O2k6ODU7cjo2ODU7aTo4NjtyOjY5MztpOjg3O3I6NzAxO2k6ODg7cjo3MDk7aTo4OTtyOjcxNztpOjkwO3I6NzI1O2k6OTE7cjo3MzM7aTo5MjtyOjc0MTtpOjkzO3I6NzQ5O2k6OTQ7cjo3NTc7aTo5NTtyOjc2NTtpOjk2O3I6NzczO2k6OTc7cjo3ODE7aTo5ODtyOjc4OTtpOjk5O3I6Nzk3O2k6MTAwO3I6ODA1O2k6MTAxO3I6ODEzO2k6MTAyO3I6ODIxO2k6MTAzO3I6ODI5O2k6MTA0O3I6ODM3O2k6MTA1O3I6ODQ1O2k6MTA2O3I6ODUzO2k6MTA3O3I6ODYxO2k6MTA4O3I6ODY5O2k6MTA5O3I6ODc3O2k6MTEwO3I6ODg1O2k6MTExO3I6ODkzO2k6MTEyO3I6OTAxO2k6MTEzO3I6OTA5O2k6MTE0O3I6OTE3O2k6MTE1O3I6OTI1O31zOjI4OiIAKgBlc2NhcGVXaGVuQ2FzdGluZ1RvU3RyaW5nIjtiOjA7fX1zOjI6ImpzIjtPOjM3OiJMYXJhdmVsXFJvc3RlclxFY29zeXN0ZW1zXEpzRWNvc3lzdGVtIjozOntzOjk6IgAqAGJ5TmFtZSI7YToxNDc6e3M6MjQ6IkBhbGNhbHpvbmUvYW5zaS10b2tlbml6ZSI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjI0OiJAYWxjYWx6b25lL2Fuc2ktdG9rZW5pemUiO3M6MTA6IgAqAHZlcnNpb24iO3M6NToiMC4zLjEiO3M6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QHJvbGxkb3duL2JpbmRpbmctYW5kcm9pZC1hcm0tZWFiaSI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjM0OiJAcm9sbGRvd24vYmluZGluZy1hbmRyb2lkLWFybS1lYWJpIjtzOjEwOiIAKgB2ZXJzaW9uIjtzOjY6IjEuMi4xMSI7czo5OiIAKgBzb3VyY2UiO3I6MTA1NztzOjY6IgAqAGRldiI7YjoxO3M6OToiACoAZGlyZWN0IjtiOjA7czoxMzoiACoAY29uc3RyYWludCI7czowOiIiO3M6NzoiACoAcGF0aCI7czo2OToiQzpcbGFyYWdvblx3d3dcY29taWRhXG5vZGVfbW9kdWxlc1xAcm9sbGRvd25cYmluZGluZy1hbmRyb2lkLWFybS1lYWJpIjt9czozMToiQHJvbGxkb3duL2JpbmRpbmctYW5kcm9pZC1hcm02NCI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjMxOiJAcm9sbGRvd24vYmluZGluZy1hbmRyb2lkLWFybTY0IjtzOjEwOiIAKgB2ZXJzaW9uIjtzOjY6IjEuMi4xMSI7czo5OiIAKgBzb3VyY2UiO3I6MTA1NztzOjY6IgAqAGRldiI7YjoxO3M6OToiACoAZGlyZWN0IjtiOjA7czoxMzoiACoAY29uc3RyYWludCI7czowOiIiO3M6NzoiACoAcGF0aCI7czo2NjoiQzpcbGFyYWdvblx3d3dcY29taWRhXG5vZGVfbW9kdWxlc1xAcm9sbGRvd25cYmluZGluZy1hbmRyb2lkLWFybTY0Ijt9czozMDoiQHJvbGxkb3duL2JpbmRpbmctZGFyd2luLWFybTY0IjtPOjIyOiJMYXJhdmVsXFJvc3RlclxQYWNrYWdlIjo3OntzOjc6IgAqAG5hbWUiO3M6MzA6IkByb2xsZG93bi9iaW5kaW5nLWRhcndpbi1hcm02NCI7czoxMDoiACoAdmVyc2lvbiI7czo2OiIxLjIuMTEiO3M6OToiACoAc291cmNlIjtyOjEwNTc7czo2OiIAKgBkZXYiO2I6MTtzOjk6IgAqAGRpcmVjdCI7YjowO3M6MTM6IgAqAGNvbnN0cmFpbnQiO3M6MDoiIjtzOjc6IgAqAHBhdGgiO3M6NjU6IkM6XGxhcmFnb25cd3d3XGNvbWlkYVxub2RlX21vZHVsZXNcQHJvbGxkb3duXGJpbmRpbmctZGFyd2luLWFybTY0Ijt9czoyODoiQHJvbGxkb3duL2JpbmRpbmctZGFyd2luLXg2NCI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjI4OiJAcm9sbGRvd24vYmluZGluZy1kYXJ3aW4teDY0IjtzOjEwOiIAKgB2ZXJzaW9uIjtzOjY6IjEuMi4xMSI7czo5OiIAKgBzb3VyY2UiO3I6MTA1NztzOjY6IgAqAGRldiI7YjoxO3M6OToiACoAZGlyZWN0IjtiOjA7czoxMzoiACoAY29uc3RyYWludCI7czowOiIiO3M6NzoiACoAcGF0aCI7czo2MzoiQzpcbGFyYWdvblx3d3dcY29taWRhXG5vZGVfbW9kdWxlc1xAcm9sbGRvd25cYmluZGluZy1kYXJ3aW4teDY0Ijt9czoyOToiQHJvbGxkb3duL2JpbmRpbmctZnJlZWJzZC14NjQiO086MjI6IkxhcmF2ZWxcUm9zdGVyXFBhY2thZ2UiOjc6e3M6NzoiACoAbmFtZSI7czoyOToiQHJvbGxkb3duL2JpbmRpbmctZnJlZWJzZC14NjQiO3M6MTA6IgAqAHZlcnNpb24iO3M6NjoiMS4yLjExIjtzOjk6IgAqAHNvdXJjZSI7cjoxMDU3O3M6NjoiACoAZGV2IjtiOjE7czo5OiIAKgBkaXJlY3QiO2I6MDtzOjEzOiIAKgBjb25zdHJhaW50IjtzOjA6IiI7czo3OiIAKgBwYXRoIjtzOjY0OiJDOlxsYXJhZ29uXHd3d1xjb21pZGFcbm9kZV9tb2R1bGVzXEByb2xsZG93blxiaW5kaW5nLWZyZWVic2QteDY0Ijt9czozNzoiQHJvbGxkb3duL2JpbmRpbmctbGludXgtYXJtLWdudWVhYmloZiI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjM3OiJAcm9sbGRvd24vYmluZGluZy1saW51eC1hcm0tZ251ZWFiaWhmIjtzOjEwOiIAKgB2ZXJzaW9uIjtzOjY6IjEuMi4xMSI7czo5OiIAKgBzb3VyY2UiO3I6MTA1NztzOjY6IgAqAGRldiI7YjoxO3M6OToiACoAZGlyZWN0IjtiOjA7czoxMzoiACoAY29uc3RyYWludCI7czowOiIiO3M6NzoiACoAcGF0aCI7czo3MjoiQzpcbGFyYWdvblx3d3dcY29taWRhXG5vZGVfbW9kdWxlc1xAcm9sbGRvd25cYmluZGluZy1saW51eC1hcm0tZ251ZWFiaWhmIjt9czozMzoiQHJvbGxkb3duL2JpbmRpbmctbGludXgtYXJtNjQtZ251IjtPOjIyOiJMYXJhdmVsXFJvc3RlclxQYWNrYWdlIjo3OntzOjc6IgAqAG5hbWUiO3M6MzM6IkByb2xsZG93bi9iaW5kaW5nLWxpbnV4LWFybTY0LWdudSI7czoxMDoiACoAdmVyc2lvbiI7czo2OiIxLjIuMTEiO3M6OToiACoAc291cmNlIjtyOjEwNTc7czo2OiIAKgBkZXYiO2I6MTtzOjk6IgAqAGRpcmVjdCI7YjowO3M6MTM6IgAqAGNvbnN0cmFpbnQiO3M6MDoiIjtzOjc6IgAqAHBhdGgiO3M6Njg6IkM6XGxhcmFnb25cd3d3XGNvbWlkYVxub2RlX21vZHVsZXNcQHJvbGxkb3duXGJpbmRpbmctbGludXgtYXJtNjQtZ251Ijt9czozNDoiQHJvbGxkb3duL2JpbmRpbmctbGludXgtYXJtNjQtbXVzbCI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjM0OiJAcm9sbGRvd24vYmluZGluZy1saW51eC1hcm02NC1tdXNsIjtzOjEwOiIAKgB2ZXJzaW9uIjtzOjY6IjEuMi4xMSI7czo5OiIAKgBzb3VyY2UiO3I6MTA1NztzOjY6IgAqAGRldiI7YjoxO3M6OToiACoAZGlyZWN0IjtiOjA7czoxMzoiACoAY29uc3RyYWludCI7czowOiIiO3M6NzoiACoAcGF0aCI7czo2OToiQzpcbGFyYWdvblx3d3dcY29taWRhXG5vZGVfbW9kdWxlc1xAcm9sbGRvd25cYmluZGluZy1saW51eC1hcm02NC1tdXNsIjt9czozMzoiQHJvbGxkb3duL2JpbmRpbmctbGludXgtcHBjNjQtZ251IjtPOjIyOiJMYXJhdmVsXFJvc3RlclxQYWNrYWdlIjo3OntzOjc6IgAqAG5hbWUiO3M6MzM6IkByb2xsZG93bi9iaW5kaW5nLWxpbnV4LXBwYzY0LWdudSI7czoxMDoiACoAdmVyc2lvbiI7czo2OiIxLjIuMTEiO3M6OToiACoAc291cmNlIjtyOjEwNTc7czo2OiIAKgBkZXYiO2I6MTtzOjk6IgAqAGRpcmVjdCI7YjowO3M6MTM6IgAqAGNvbnN0cmFpbnQiO3M6MDoiIjtzOjc6IgAqAHBhdGgiO3M6Njg6IkM6XGxhcmFnb25cd3d3XGNvbWlkYVxub2RlX21vZHVsZXNcQHJvbGxkb3duXGJpbmRpbmctbGludXgtcHBjNjQtZ251Ijt9czozMzoiQHJvbGxkb3duL2JpbmRpbmctbGludXgtczM5MHgtZ251IjtPOjIyOiJMYXJhdmVsXFJvc3RlclxQYWNrYWdlIjo3OntzOjc6IgAqAG5hbWUiO3M6MzM6IkByb2xsZG93bi9iaW5kaW5nLWxpbnV4LXMzOTB4LWdudSI7czoxMDoiACoAdmVyc2lvbiI7czo2OiIxLjIuMTEiO3M6OToiACoAc291cmNlIjtyOjEwNTc7czo2OiIAKgBkZXYiO2I6MTtzOjk6IgAqAGRpcmVjdCI7YjowO3M6MTM6IgAqAGNvbnN0cmFpbnQiO3M6MDoiIjtzOjc6IgAqAHBhdGgiO3M6Njg6IkM6XGxhcmFnb25cd3d3XGNvbWlkYVxub2RlX21vZHVsZXNcQHJvbGxkb3duXGJpbmRpbmctbGludXgtczM5MHgtZ251Ijt9czozMToiQHJvbGxkb3duL2JpbmRpbmctbGludXgteDY0LWdudSI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjMxOiJAcm9sbGRvd24vYmluZGluZy1saW51eC14NjQtZ251IjtzOjEwOiIAKgB2ZXJzaW9uIjtzOjY6IjEuMi4xMSI7czo5OiIAKgBzb3VyY2UiO3I6MTA1NztzOjY6IgAqAGRldiI7YjoxO3M6OToiACoAZGlyZWN0IjtiOjA7czoxMzoiACoAY29uc3RyYWludCI7czowOiIiO3M6NzoiACoAcGF0aCI7czo2NjoiQzpcbGFyYWdvblx3d3dcY29taWRhXG5vZGVfbW9kdWxlc1xAcm9sbGRvd25cYmluZGluZy1saW51eC14NjQtZ251Ijt9czozMjoiQHJvbGxkb3duL2JpbmRpbmctbGludXgteDY0LW11c2wiO086MjI6IkxhcmF2ZWxcUm9zdGVyXFBhY2thZ2UiOjc6e3M6NzoiACoAbmFtZSI7czozMjoiQHJvbGxkb3duL2JpbmRpbmctbGludXgteDY0LW11c2wiO3M6MTA6IgAqAHZlcnNpb24iO3M6NjoiMS4yLjExIjtzOjk6IgAqAHNvdXJjZSI7cjoxMDU3O3M6NjoiACoAZGV2IjtiOjE7czo5OiIAKgBkaXJlY3QiO2I6MDtzOjEzOiIAKgBjb25zdHJhaW50IjtzOjA6IiI7czo3OiIAKgBwYXRoIjtzOjY3OiJDOlxsYXJhZ29uXHd3d1xjb21pZGFcbm9kZV9tb2R1bGVzXEByb2xsZG93blxiaW5kaW5nLWxpbnV4LXg2NC1tdXNsIjt9czozNToiQHJvbGxkb3duL2JpbmRpbmctb3Blbmhhcm1vbnktYXJtNjQiO086MjI6IkxhcmF2ZWxcUm9zdGVyXFBhY2thZ2UiOjc6e3M6NzoiACoAbmFtZSI7czozNToiQHJvbGxkb3duL2JpbmRpbmctb3Blbmhhcm1vbnktYXJtNjQiO3M6MTA6IgAqAHZlcnNpb24iO3M6NjoiMS4yLjExIjtzOjk6IgAqAHNvdXJjZSI7cjoxMDU3O3M6NjoiACoAZGV2IjtiOjE7czo5OiIAKgBkaXJlY3QiO2I6MDtzOjEzOiIAKgBjb25zdHJhaW50IjtzOjA6IiI7czo3OiIAKgBwYXRoIjtzOjcwOiJDOlxsYXJhZ29uXHd3d1xjb21pZGFcbm9kZV9tb2R1bGVzXEByb2xsZG93blxiaW5kaW5nLW9wZW5oYXJtb255LWFybTY0Ijt9czozNDoiQHJvbGxkb3duL2JpbmRpbmctd2luMzItYXJtNjQtbXN2YyI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjM0OiJAcm9sbGRvd24vYmluZGluZy13aW4zMi1hcm02NC1t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blx3d3dcY29taWRhXG5vZGVfbW9kdWxlc1xAdGFpbHdpbmRjc3Ncb3hpZGUtZnJlZWJzZC14NjQiO31zOjM4OiJAdGFpbHdpbmRjc3Mvb3hpZGUtbGludXgtYXJtLWdudWVhYmloZiI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjM4OiJAdGFpbHdpbmRjc3Mvb3hpZGUtbGludXgtYXJtLWdudWVhYmloZiI7czoxMDoiACoAdmVyc2lvbiI7czo1OiI0LjMuMyI7czo5OiIAKgBzb3VyY2UiO3I6MTA1NztzOjY6IgAqAGRldiI7YjoxO3M6OToiACoAZGlyZWN0IjtiOjA7czoxMzoiACoAY29uc3RyYWludCI7czowOiIiO3M6NzoiACoAcGF0aCI7czo3MzoiQzpcbGFyYWdvblx3d3dcY29taWRhXG5vZGVfbW9kdWxlc1xAdGFpbHdpbmRjc3Ncb3hpZGUtbGludXgtYXJtLWdudWVhYmloZiI7fXM6MzQ6IkB0YWlsd2luZGNzcy9veGlkZS1saW51eC1hcm02NC1nbnUiO086MjI6IkxhcmF2ZWxcUm9zdGVyXFBhY2thZ2UiOjc6e3M6NzoiACoAbmFtZSI7czozNDoiQHRhaWx3aW5kY3NzL294aWRlLWxpbnV4LWFybTY0LWdudSI7czoxMDoiACoAdmVyc2lvbiI7czo1OiI0LjMuMyI7czo5OiIAKgBzb3VyY2UiO3I6MTA1NztzOjY6IgAqAGRldiI7YjoxO3M6OToiACoAZGlyZWN0IjtiOjA7czoxMzoiACoAY29uc3RyYWludCI7czowOiIiO3M6NzoiACoAcGF0aCI7czo2OToiQzpcbGFyYWdvblx3d3dcY29taWRhXG5vZGVfbW9kdWxlc1xAdGFpbHdpbmRjc3Ncb3hpZGUtbGludXgtYXJtNjQtZ251Ijt9czozNToiQHRhaWx3aW5kY3NzL294aWRlLWxpbnV4LWFybTY0LW11c2wiO086MjI6IkxhcmF2ZWxcUm9zdGVyXFBhY2thZ2UiOjc6e3M6NzoiACoAbmFtZSI7czozNToiQHRhaWx3aW5kY3NzL294aWRlLWxpbnV4LWFybTY0LW11c2wiO3M6MTA6IgAqAHZlcnNpb24iO3M6NToiNC4zLjMiO3M6OToiACoAc291cmNlIjtyOjEwNTc7czo2OiIAKgBkZXYiO2I6MTtzOjk6IgAqAGRpcmVjdCI7YjowO3M6MTM6IgAqAGNvbnN0cmFpbnQiO3M6MDoiIjtzOjc6IgAqAHBhdGgiO3M6NzA6IkM6XGxhcmFnb25cd3d3XGNvbWlkYVxub2RlX21vZHVsZXNcQHRhaWx3aW5kY3NzXG94aWRlLWxpbnV4LWFybTY0LW11c2wiO31zOjMyOiJAdGFpbHdpbmRjc3Mvb3hpZGUtbGludXgteDY0LWdudSI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjMyOiJAdGFpbHdpbmRjc3Mvb3hpZGUtbGludXgteDY0LWdudSI7czoxMDoiACoAdmVyc2lvbiI7czo1OiI0LjMuMyI7czo5OiIAKgBzb3VyY2UiO3I6MTA1NztzOjY6IgAqAGRldiI7YjoxO3M6OToiACoAZGlyZWN0IjtiOjA7czoxMzoiACoAY29uc3RyYWludCI7czowOiIiO3M6NzoiACoAcGF0aCI7czo2NzoiQzpcbGFyYWdvblx3d3dcY29taWRhXG5vZGVfbW9kdWxlc1xAdGFpbHdpbmRjc3Ncb3hpZGUtbGludXgteDY0LWdudSI7fXM6MzM6IkB0YWlsd2luZGNzcy9veGlkZS1saW51eC14NjQtbXVzbCI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjMzOiJAdGFpbHdpbmRjc3Mvb3hpZGUtbGludXgteDY0LW11c2wiO3M6MTA6IgAqAHZlcnNpb24iO3M6NToiNC4zLjMiO3M6OToiACoAc291cmNlIjtyOjEwNTc7czo2OiIAKgBkZXYiO2I6MTtzOjk6IgAqAGRpcmVjdCI7YjowO3M6MTM6IgAqAGNvbnN0cmFpbnQiO3M6MDoiIjtzOjc6IgAqAHBhdGgiO3M6Njg6IkM6XGxhcmFnb25cd3d3XGNvbWlkYVxub2RlX21vZHVsZXNcQHRhaWx3aW5kY3NzXG94aWRlLWxpbnV4LXg2NC1tdXNsIjt9czozMDoiQHRhaWx3aW5kY3NzL294aWRlLXdhc20z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NzoiACoAcGF0aCI7czo2NzoiQzpcbGFyYWdvblx3d3dcY29taWRhXG5vZGVfbW9kdWxlc1xsaWdodG5pbmdjc3MtbGludXgtYXJtLWdudWVhYmloZiI7fXM6Mjg6ImxpZ2h0bmluZ2Nzcy1saW51eC1hcm02NC1nbnUiO086MjI6IkxhcmF2ZWxcUm9zdGVyXFBhY2thZ2UiOjc6e3M6NzoiACoAbmFtZSI7czoyODoibGlnaHRuaW5nY3NzLWxpbnV4LWFybTY0LWdudSI7czoxMDoiACoAdmVyc2lvbiI7czo2OiIxLjMyLjAiO3M6OToiACoAc291cmNlIjtyOjEwNTc7czo2OiIAKgBkZXYiO2I6MTtzOjk6IgAqAGRpcmVjdCI7YjowO3M6MTM6IgAqAGNvbnN0cmFpbnQiO3M6MDoiIjtzOjc6IgAqAHBhdGgiO3M6NjM6IkM6XGxhcmFnb25cd3d3XGNvbWlkYVxub2RlX21vZHVsZXNcbGlnaHRuaW5nY3NzLWxpbnV4LWFybTY0LWdudSI7fXM6Mjk6ImxpZ2h0bmluZ2Nzcy1saW51eC1hcm02NC1tdXNsIjtPOjIyOiJMYXJhdmVsXFJvc3RlclxQYWNrYWdlIjo3OntzOjc6IgAqAG5hbWUiO3M6Mjk6ImxpZ2h0bmluZ2Nzcy1saW51eC1hcm02NC1tdXNsIjtzOjEwOiIAKgB2ZXJzaW9uIjtzOjY6IjEuMzIuMCI7czo5OiIAKgBzb3VyY2UiO3I6MTA1NztzOjY6IgAqAGRldiI7YjoxO3M6OToiACoAZGlyZWN0IjtiOjA7czoxMzoiACoAY29uc3RyYWludCI7czowOiIiO3M6NzoiACoAcGF0aCI7czo2NDoiQzpcbGFyYWdvblx3d3dcY29taWRhXG5vZGVfbW9kdWxlc1xsaWdodG5pbmdjc3MtbGludXgtYXJtNjQtbXVzbCI7fXM6MjY6ImxpZ2h0bmluZ2Nzcy1saW51eC14NjQtZ251IjtPOjIyOiJMYXJhdmVsXFJvc3RlclxQYWNrYWdlIjo3OntzOjc6IgAqAG5hbWUiO3M6MjY6ImxpZ2h0bmluZ2Nzcy1saW51eC14NjQtZ251IjtzOjEwOiIAKgB2ZXJzaW9uIjtzOjY6IjEuMzIuMCI7czo5OiIAKgBzb3VyY2UiO3I6MTA1NztzOjY6IgAqAGRldiI7YjoxO3M6OToiACoAZGlyZWN0IjtiOjA7czoxMzoiACoAY29uc3RyYWludCI7czowOiIiO3M6NzoiACoAcGF0aCI7czo2MToiQzpcbGFyYWdvblx3d3dcY29taWRhXG5vZGVfbW9kdWxlc1xsaWdodG5pbmdjc3MtbGludXgteDY0LWdudSI7fXM6Mjc6ImxpZ2h0bmluZ2Nzcy1saW51eC14NjQtbXVzbCI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjI3OiJsaWdodG5pbmdjc3MtbGludXgteDY0LW11c2wiO3M6MTA6IgAqAHZlcnNpb24iO3M6NjoiMS4zMi4wIjtzOjk6IgAqAHNvdXJjZSI7cjoxMDU3O3M6NjoiACoAZGV2IjtiOjE7czo5OiIAKgBkaXJlY3QiO2I6MDtzOjEzOiIAKgBjb25zdHJhaW50IjtzOjA6IiI7czo3OiIAKgBwYXRoIjtzOjYyOiJDOlxsYXJhZ29uXHd3d1xjb21pZGFcbm9kZV9tb2R1bGVzXGxpZ2h0bmluZ2Nzcy1saW51eC14NjQtbXVzbCI7fXM6Mjk6ImxpZ2h0bmluZ2Nzcy13aW4zMi1hcm02NC1tc3ZjIjtPOjIyOiJMYXJhdmVsXFJvc3RlclxQYWNrYWdlIjo3OntzOjc6IgAqAG5hbWUiO3M6Mjk6ImxpZ2h0bmluZ2Nzcy13aW4zMi1hcm02NC1tc3ZjIjtzOjEwOiIAKgB2ZXJzaW9uIjtzOjY6IjEuMzIuMCI7czo5OiIAKgBzb3VyY2UiO3I6MTA1NztzOjY6IgAqAGRldiI7YjoxO3M6OToiACoAZGlyZWN0IjtiOjA7czoxMzoiACoAY29uc3RyYWludCI7czowOiIiO3M6NzoiACoAcGF0aCI7czo2NDoiQzpcbGFyYWdvblx3d3dcY29taWRhXG5vZGVfbW9kdWxlc1xsaWdodG5pbmdjc3Mtd2luMzItYXJtNjQtbXN2YyI7fXM6Mjc6ImxpZ2h0bmluZ2Nzcy13aW4zMi14NjQtbXN2YyI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjI3OiJsaWdodG5pbmdjc3Mtd2luMzIteDY0LW1zdmMiO3M6MTA6IgAqAHZlcnNpb24iO3M6NjoiMS4zMi4wIjtzOjk6IgAqAHNvdXJjZSI7cjoxMDU3O3M6NjoiACoAZGV2IjtiOjE7czo5OiIAKgBkaXJlY3QiO2I6MDtzOjEzOiIAKgBjb25zdHJhaW50IjtzOjA6IiI7czo3OiIAKgBwYXRoIjtzOjYyOiJDOlxsYXJhZ29uXHd3d1xjb21pZGFcbm9kZV9tb2R1bGVzXGxpZ2h0bmluZ2Nzcy13aW4zMi14NjQtbXN2YyI7fXM6MTI6Im1hZ2ljLXN0cmluZyI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjEyOiJtYWdpYy1zdHJpbmciO3M6MTA6IgAqAHZlcnNpb24iO3M6NzoiMC4zMC4yMSI7czo5OiIAKgBzb3VyY2UiO3I6MTA1NztzOjY6IgAqAGRldiI7YjoxO3M6OToiACoAZGlyZWN0IjtiOjA7czoxMzoiACoAY29uc3RyYWludCI7czowOiIiO3M6NzoiACoAcGF0aCI7czo0NzoiQzpcbGFyYWdvblx3d3dcY29taWRhXG5vZGVfbW9kdWxlc1xtYWdpYy1zdHJpbmciO31zOjY6Im5hbm9pZCI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjY6Im5hbm9pZCI7czoxMDoiACoAdmVyc2lvbiI7czo2OiIzLjMuMTkiO3M6OToiACoAc291cmNlIjtyOjEwNTc7czo2OiIAKgBkZXYiO2I6MTtzOjk6IgAqAGRpcmVjdCI7YjowO3M6MTM6IgAqAGNvbnN0cmFpbnQiO3M6MDoiIjtzOjc6IgAqAHBhdGgiO3M6NDE6IkM6XGxhcmFnb25cd3d3XGNvbWlkYVxub2RlX21vZHVsZXNcbmFub2lkIjt9czoxMDoicGljb2NvbG9ycyI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjEwOiJwaWNvY29sb3JzIjtzOjEwOiIAKgB2ZXJzaW9uIjtzOjU6IjEuMS4xIjtzOjk6IgAqAHNvdXJjZSI7cjoxMDU3O3M6NjoiACoAZGV2IjtiOjE7czo5OiIAKgBkaXJlY3QiO2I6MDtzOjEzOiIAKgBjb25zdHJhaW50IjtzOjA6IiI7czo3OiIAKgBwYXRoIjtzOjQ1OiJDOlxsYXJhZ29uXHd3d1xjb21pZGFcbm9kZV9tb2R1bGVzXHBpY29jb2xvcnMiO31zOjk6InBpY29tYXRjaCI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjk6InBpY29tYXRjaCI7czoxMDoiACoAdmVyc2lvbiI7czo1OiI0LjAuNyI7czo5OiIAKgBzb3VyY2UiO3I6MTA1NztzOjY6IgAqAGRldiI7YjoxO3M6OToiACoAZGlyZWN0IjtiOjA7czoxMzoiACoAY29uc3RyYWludCI7czowOiIiO3M6NzoiACoAcGF0aCI7czo0NDoiQzpcbGFyYWdvblx3d3dcY29taWRhXG5vZGVfbW9kdWxlc1xwaWNvbWF0Y2giO31zOjc6InBvc3Rjc3MiO086MjI6IkxhcmF2ZWxcUm9zdGVyXFBhY2thZ2UiOjc6e3M6NzoiACoAbmFtZSI7czo3OiJwb3N0Y3NzIjtzOjEwOiIAKgB2ZXJzaW9uIjtzOjY6IjguNS4yOCI7czo5OiIAKgBzb3VyY2UiO3I6MTA1NztzOjY6IgAqAGRldiI7YjoxO3M6OToiACoAZGlyZWN0IjtiOjA7czoxMzoiACoAY29uc3RyYWludCI7czowOiIiO3M6NzoiACoAcGF0aCI7czo0MjoiQzpcbGFyYWdvblx3d3dcY29taWRhXG5vZGVfbW9kdWxlc1xwb3N0Y3NzIjt9czo4OiJyb2xsZG93biI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjg6InJvbGxkb3duIjtzOjEwOiIAKgB2ZXJzaW9uIjtzOjY6IjEuMi4xMSI7czo5OiIAKgBzb3VyY2UiO3I6MTA1NztzOjY6IgAqAGRldiI7YjoxO3M6OToiACoAZGlyZWN0IjtiOjA7czoxMzoiACoAY29uc3RyYWludCI7czowOiIiO3M6NzoiACoAcGF0aCI7czo0MzoiQzpcbGFyYWdvblx3d3dcY29taWRhXG5vZGVfbW9kdWxlc1xyb2xsZG93biI7fXM6NDoicnhqcyI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjQ6InJ4anMiO3M6MTA6IgAqAHZlcnNpb24iO3M6NToiNy44LjIiO3M6OToiACoAc291cmNlIjtyOjEwNTc7czo2OiIAKgBkZXYiO2I6MTtzOjk6IgAqAGRpcmVjdCI7YjowO3M6MTM6IgAqAGNvbnN0cmFpbnQiO3M6MDoiIjtzOjc6IgAqAHBhdGgiO3M6Mzk6IkM6XGxhcmFnb25cd3d3XGNvbWlkYVxub2RlX21vZHVsZXNccnhqcyI7fXM6MTE6InNoZWxsLXF1b3RlIjtPOjIyOiJMYXJhdmVsXFJvc3RlclxQYWNrYWdlIjo3OntzOjc6IgAqAG5hbWUiO3M6MTE6InNoZWxsLXF1b3RlIjtzOjEwOiIAKgB2ZXJzaW9uIjtzOjU6IjEuOS4wIjtzOjk6IgAqAHNvdXJjZSI7cjoxMDU3O3M6NjoiACoAZGV2IjtiOjE7czo5OiIAKgBkaXJlY3QiO2I6MDtzOjEzOiIAKgBjb25zdHJhaW50IjtzOjA6IiI7czo3OiIAKgBwYXRoIjtzOjQ2OiJDOlxsYXJhZ29uXHd3d1xjb21pZGFcbm9kZV9tb2R1bGVzXHNoZWxsLXF1b3RlIjt9czoxMzoic291cmNlLW1hcC1qcyI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjEzOiJzb3VyY2UtbWFwLWpzIjtzOjEwOiIAKgB2ZXJzaW9uIjtzOjU6IjEuMi4xIjtzOjk6IgAqAHNvdXJjZSI7cjoxMDU3O3M6NjoiACoAZGV2IjtiOjE7czo5OiIAKgBkaXJlY3QiO2I6MDtzOjEzOiIAKgBjb25zdHJhaW50IjtzOjA6IiI7czo3OiIAKgBwYXRoIjtzOjQ4OiJDOlxsYXJhZ29uXHd3d1xjb21pZGFcbm9kZV9tb2R1bGVzXHNvdXJjZS1tYXAtanMiO31zOjE0OiJzdXBwb3J0cy1jb2xvciI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjE0OiJzdXBwb3J0cy1jb2xvciI7czoxMDoiACoAdmVyc2lvbiI7czo2OiIxMC4yLjIiO3M6OToiACoAc291cmNlIjtyOjEwNTc7czo2OiIAKgBkZXYiO2I6MTtzOjk6IgAqAGRpcmVjdCI7YjowO3M6MTM6IgAqAGNvbnN0cmFpbnQiO3M6MDoiIjtzOjc6IgAqAHBhdGgiO3M6NDk6IkM6XGxhcmFnb25cd3d3XGNvbWlkYVxub2RlX21vZHVsZXNcc3VwcG9ydHMtY29sb3IiO31zOjExOiJ0YWlsd2luZGNzcyI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjExOiJ0YWlsd2luZGNzcyI7czoxMDoiACoAdmVyc2lvbiI7czo1OiI0LjMuMyI7czo5OiIAKgBzb3VyY2UiO3I6MTA1NztzOjY6IgAqAGRldiI7YjoxO3M6OToiACoAZGlyZWN0IjtiOjE7czoxMzoiACoAY29uc3RyYWludCI7czo2OiJeNC4zLjMiO3M6NzoiACoAcGF0aCI7czo0NjoiQzpcbGFyYWdvblx3d3dcY29taWRhXG5vZGVfbW9kdWxlc1x0YWlsd2luZGNzcyI7fXM6NzoidGFwYWJsZSI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjc6InRhcGFibGUiO3M6MTA6IgAqAHZlcnNpb24iO3M6NToiMi4zLjMiO3M6OToiACoAc291cmNlIjtyOjEwNTc7czo2OiIAKgBkZXYiO2I6MTtzOjk6IgAqAGRpcmVjdCI7YjowO3M6MTM6IgAqAGNvbnN0cmFpbnQiO3M6MDoiIjtzOjc6IgAqAHBhdGgiO3M6NDI6IkM6XGxhcmFnb25cd3d3XGNvbWlkYVxub2RlX21vZHVsZXNcdGFwYWJsZSI7fXM6MTA6InRpbnlnbG9iYnkiO086MjI6IkxhcmF2ZWxcUm9zdGVyXFBhY2thZ2UiOjc6e3M6NzoiACoAbmFtZSI7czoxMDoidGlueWdsb2JieSI7czoxMDoiACoAdmVyc2lvbiI7czo2OiIwLjIuMTciO3M6OToiACoAc291cmNlIjtyOjEwNTc7czo2OiIAKgBkZXYiO2I6MTtzOjk6IgAqAGRpcmVjdCI7YjowO3M6MTM6IgAqAGNvbnN0cmFpbnQiO3M6MDoiIjtzOjc6IgAqAHBhdGgiO3M6NDU6IkM6XGxhcmFnb25cd3d3XGNvbWlkYVxub2RlX21vZHVsZXNcdGlueWdsb2JieSI7fXM6OToidHJlZS1raWxsIjtPOjIyOiJMYXJhdmVsXFJvc3RlclxQYWNrYWdlIjo3OntzOjc6IgAqAG5hbWUiO3M6OToidHJlZS1raWxsIjtzOjEwOiIAKgB2ZXJzaW9uIjtzOjU6IjEuMi4yIjtzOjk6IgAqAHNvdXJjZSI7cjoxMDU3O3M6NjoiACoAZGV2IjtiOjE7czo5OiIAKgBkaXJlY3QiO2I6MDtzOjEzOiIAKgBjb25zdHJhaW50IjtzOjA6IiI7czo3OiIAKgBwYXRoIjtzOjQ0OiJDOlxsYXJhZ29uXHd3d1xjb21pZGFcbm9kZV9tb2R1bGVzXHRyZWUta2lsbCI7fXM6NToidHNsaWIiO086MjI6IkxhcmF2ZWxcUm9zdGVyXFBhY2thZ2UiOjc6e3M6NzoiACoAbmFtZSI7czo1OiJ0c2xpYiI7czoxMDoiACoAdmVyc2lvbiI7czo1OiIyLjguMSI7czo5OiIAKgBzb3VyY2UiO3I6MTA1NztzOjY6IgAqAGRldiI7YjoxO3M6OToiACoAZGlyZWN0IjtiOjA7czoxMzoiACoAY29uc3RyYWludCI7czowOiIiO3M6NzoiACoAcGF0aCI7czo0MDoiQzpcbGFyYWdvblx3d3dcY29taWRhXG5vZGVfbW9kdWxlc1x0c2xpYiI7fXM6NDoidml0ZSI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjQ6InZpdGUiO3M6MTA6IgAqAHZlcnNpb24iO3M6NToiOC4zLjEiO3M6OToiACoAc291cmNlIjtyOjEwNTc7czo2OiIAKgBkZXYiO2I6MTtzOjk6IgAqAGRpcmVjdCI7YjoxO3M6MTM6IgAqAGNvbnN0cmFpbnQiO3M6NjoiXjguMC4wIjtzOjc6IgAqAHBhdGgiO3M6Mzk6IkM6XGxhcmFnb25cd3d3XGNvbWlkYVxub2RlX21vZHVsZXNcdml0ZSI7fXM6MjM6InZpdGUtcGx1Z2luLWZ1bGwtcmVsb2FkIjtPOjIyOiJMYXJhdmVsXFJvc3RlclxQYWNrYWdlIjo3OntzOjc6IgAqAG5hbWUiO3M6MjM6InZpdGUtcGx1Z2luLWZ1bGwtcmVsb2FkIjtzOjEwOiIAKgB2ZXJzaW9uIjtzOjU6IjEuMi4wIjtzOjk6IgAqAHNvdXJjZSI7cjoxMDU3O3M6NjoiACoAZGV2IjtiOjE7czo5OiIAKgBkaXJlY3QiO2I6MDtzOjEzOiIAKgBjb25zdHJhaW50IjtzOjA6IiI7czo3OiIAKgBwYXRoIjtzOjU4OiJDOlxsYXJhZ29uXHd3d1xjb21pZGFcbm9kZV9tb2R1bGVzXHZpdGUtcGx1Z2luLWZ1bGwtcmVsb2FkIjt9czo0OiJ5MThuIjtPOjIyOiJMYXJhdmVsXFJvc3RlclxQYWNrYWdlIjo3OntzOjc6IgAqAG5hbWUiO3M6NDoieTE4biI7czoxMDoiACoAdmVyc2lvbiI7czo1OiI1LjAuOCI7czo5OiIAKgBzb3VyY2UiO3I6MTA1NztzOjY6IgAqAGRldiI7YjoxO3M6OToiACoAZGlyZWN0IjtiOjA7czoxMzoiACoAY29uc3RyYWludCI7czowOiIiO3M6NzoiACoAcGF0aCI7czozOToiQzpcbGFyYWdvblx3d3dcY29taWRhXG5vZGVfbW9kdWxlc1x5MThuIjt9czo1OiJ5YXJncyI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjU6InlhcmdzIjtzOjEwOiIAKgB2ZXJzaW9uIjtzOjY6IjE4LjAuMCI7czo5OiIAKgBzb3VyY2UiO3I6MTA1NztzOjY6IgAqAGRldiI7YjoxO3M6OToiACoAZGlyZWN0IjtiOjA7czoxMzoiACoAY29uc3RyYWludCI7czowOiIiO3M6NzoiACoAcGF0aCI7czo0MDoiQzpcbGFyYWdvblx3d3dcY29taWRhXG5vZGVfbW9kdWxlc1x5YXJncyI7fXM6MTI6InlhcmdzLXBhcnNlciI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjEyOiJ5YXJncy1wYXJzZXIiO3M6MTA6IgAqAHZlcnNpb24iO3M6NjoiMjIuMC4wIjtzOjk6IgAqAHNvdXJjZSI7cjoxMDU3O3M6NjoiACoAZGV2IjtiOjE7czo5OiIAKgBkaXJlY3QiO2I6MDtzOjEzOiIAKgBjb25zdHJhaW50IjtzOjA6IiI7czo3OiIAKgBwYXRoIjtzOjQ3OiJDOlxsYXJhZ29uXHd3d1xjb21pZGFcbm9kZV9tb2R1bGVzXHlhcmdzLXBhcnNlciI7fX1zOjExOiIAKgBwYWNrYWdlcyI7TzozMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZUNvbGxlY3Rpb24iOjI6e3M6ODoiACoAaXRlbXMiO2E6MTQ3OntpOjA7cjoxMDU0O2k6MTtyOjEwNjI7aToyO3I6MTA3MDtpOjM7cjoxMDc4O2k6NDtyOjEwODY7aTo1O3I6MTA5NDtpOjY7cjoxMTAyO2k6NztyOjExMTA7aTo4O3I6MTExODtpOjk7cjoxMTI2O2k6MTA7cjoxMTM0O2k6MTE7cjoxMTQyO2k6MTI7cjoxMTUwO2k6MTM7cjoxMTU4O2k6MTQ7cjoxMTY2O2k6MTU7cjoxMTc0O2k6MTY7cjoxMTgyO2k6MTc7cjoxMTkwO2k6MTg7cjoxMTk4O2k6MTk7cjoxMjA2O2k6MjA7cjoxMjE0O2k6MjE7cjoxMjIyO2k6MjI7cjoxMjMwO2k6MjM7cjoxMjM4O2k6MjQ7cjoxMjQ2O2k6MjU7cjoxMjU0O2k6MjY7cjoxMjYyO2k6Mjc7cjoxMjcwO2k6Mjg7cjoxMjc4O2k6Mjk7cjoxMjg2O2k6MzA7cjoxMjk0O2k6MzE7cjoxMzAyO2k6MzI7cjoxMzEwO2k6MzM7cjoxMzE4O2k6MzQ7cjoxMzI2O2k6MzU7cjoxMzM0O2k6MzY7cjoxMzQyO2k6Mzc7cjoxMzUwO2k6Mzg7cjoxMzU4O2k6Mzk7cjoxMzY2O2k6NDA7cjoxMzc0O2k6NDE7cjoxMzgyO2k6NDI7cjoxMzkwO2k6NDM7cjoxMzk4O2k6NDQ7cjoxNDA2O2k6NDU7cjoxNDE0O2k6NDY7cjoxNDIyO2k6NDc7cjoxNDMwO2k6NDg7cjoxNDM4O2k6NDk7cjoxNDQ2O2k6NTA7cjoxNDU0O2k6NTE7cjoxNDYyO2k6NTI7cjoxNDcwO2k6NTM7cjoxNDc4O2k6NTQ7cjoxNDg2O2k6NTU7cjoxNDk0O2k6NTY7cjoxNTAyO2k6NTc7cjoxNTEwO2k6NTg7cjoxNTE4O2k6NTk7cjoxNTI2O2k6NjA7cjoxNTM0O2k6NjE7cjoxNTQyO2k6NjI7cjoxNTUwO2k6NjM7cjoxNTU4O2k6NjQ7cjoxNTY2O2k6NjU7cjoxNTc0O2k6NjY7cjoxNTgyO2k6Njc7cjoxNTkwO2k6Njg7cjoxNTk4O2k6Njk7cjoxNjA2O2k6NzA7cjoxNjE0O2k6NzE7cjoxNjIyO2k6NzI7cjoxNjMwO2k6NzM7cjoxNjM4O2k6NzQ7cjoxNjQ2O2k6NzU7cjoxNjU0O2k6NzY7cjoxNjYyO2k6Nzc7cjoxNjcwO2k6Nzg7cjoxNjc4O2k6Nzk7cjoxNjg2O2k6ODA7cjoxNjk0O2k6ODE7cjoxNzAyO2k6ODI7cjoxNzEwO2k6ODM7cjoxNzE4O2k6ODQ7cjoxNzI2O2k6ODU7cjoxNzM0O2k6ODY7cjoxNzQyO2k6ODc7cjoxNzUwO2k6ODg7cjoxNzU4O2k6ODk7cjoxNzY2O2k6OTA7cjoxNzc0O2k6OTE7cjoxNzgyO2k6OTI7cjoxNzkwO2k6OTM7cjoxNzk4O2k6OTQ7cjoxODA2O2k6OTU7cjoxODE0O2k6OTY7cjoxODIyO2k6OTc7cjoxODMwO2k6OTg7cjoxODM4O2k6OTk7cjoxODQ2O2k6MTAwO3I6MTg1NDtpOjEwMTtyOjE4NjI7aToxMDI7cjoxODcwO2k6MTAzO3I6MTg3ODtpOjEwNDtyOjE4ODY7aToxMDU7cjoxODk0O2k6MTA2O3I6MTkwMjtpOjEwNztyOjE5MTA7aToxMDg7cjoxOTE4O2k6MTA5O3I6MTkyNjtpOjExMDtyOjE5MzQ7aToxMTE7cjoxOTQyO2k6MTEyO3I6MTk1MDtpOjExMztyOjE5NTg7aToxMTQ7cjoxOTY2O2k6MTE1O3I6MTk3NDtpOjExNjtyOjE5ODI7aToxMTc7cjoxOTkwO2k6MTE4O3I6MTk5ODtpOjExOTtyOjIwMDY7aToxMjA7cjoyMDE0O2k6MTIxO3I6MjAyMjtpOjEyMjtyOjIwMzA7aToxMjM7cjoyMDM4O2k6MTI0O3I6MjA0NjtpOjEyNTtyOjIwNTQ7aToxMjY7cjoyMDYyO2k6MTI3O3I6MjA3MDtpOjEyODtyOjIwNzg7aToxMjk7cjoyMDg2O2k6MTMwO3I6MjA5NDtpOjEzMTtyOjIxMDI7aToxMzI7cjoyMTEwO2k6MTMzO3I6MjExODtpOjEzNDtyOjIxMjY7aToxMzU7cjoyMTM0O2k6MTM2O3I6MjE0MjtpOjEzNztyOjIxNTA7aToxMzg7cjoyMTU4O2k6MTM5O3I6MjE2NjtpOjE0MDtyOjIxNzQ7aToxNDE7cjoyMTgyO2k6MTQyO3I6MjE5MDtpOjE0MztyOjIxOTg7aToxNDQ7cjoyMjA2O2k6MTQ1O3I6MjIxNDtpOjE0NjtyOjIyMjI7fXM6Mjg6IgAqAGVzY2FwZVdoZW5DYXN0aW5nVG9TdHJpbmciO2I6MDt9czoxNzoiACoAcGFja2FnZU1hbmFnZXIiO0U6NDE6IkxhcmF2ZWxcUm9zdGVyXEVudW1zXEpzUGFja2FnZU1hbmFnZXI6TnBtIjt9czo2OiJzdGFja3MiO086MzA6IkxhcmF2ZWxcUm9zdGVyXFN1cHBvcnRcRW51bVNldCI6MTp7czo4OiIAKgBjYXNlcyI7YToxOntpOjA7RTozMDoiTGFyYXZlbFxSb3N0ZXJcRW51bXNcU3RhY2s6QXBpIjt9fXM6MjE6ImJyb3dzZXJUZXN0RnJhbWV3b3JrcyI7TzozMDoiTGFyYXZlbFxSb3N0ZXJcU3VwcG9ydFxFbnVtU2V0IjoxOntzOjg6IgAqAGNhc2VzIjthOjA6e319czo5OiJmcm9udGVuZHMiO086MzA6IkxhcmF2ZWxcUm9zdGVyXFN1cHBvcnRcRW51bVNldCI6MTp7czo4OiIAKgBjYXNlcyI7YTowOnt9fXM6NjoiYWdlbnRzIjtPOjMwOiJMYXJhdmVsXFJvc3RlclxTdXBwb3J0XEVudW1TZXQiOjE6e3M6ODoiACoAY2FzZXMiO2E6Mjp7aTowO0U6Mzc6IkxhcmF2ZWxcUm9zdGVyXEVudW1zXEFnZW50OkNsYXVkZUNvZGUiO2k6MTtFOjMyOiJMYXJhdmVsXFJvc3RlclxFbnVtc1xBZ2VudDpDb2RleCI7fX1zOjc6ImVkaXRvcnMiO086MzA6IkxhcmF2ZWxcUm9zdGVyXFN1cHBvcnRcRW51bVNldCI6MTp7czo4OiIAKgBjYXNlcyI7YTowOnt9fX0=	1791343649
sazon-cache-admin@recetas.com|127.0.0.1:timer	i:1791398374;	1791398374
sazon-cache-admin@recetas.com|127.0.0.1	i:1;	1791398375
sazon-cache-roster:project:v3:46a79cc4bf3840f0c2e9011cc51dcef0	TzoyNjoiTGFyYXZlbFxSb3N0ZXJcUHJvamVjdFNjYW4iOjg6e3M6ODoiYmFzZVBhdGgiO3M6MjI6IkM6XGxhcmFnb25cd3d3XGNvbWlkYVwiO3M6MzoicGhwIjtPOjM1OiJMYXJhdmVsXFJvc3RlclxFY29zeXN0ZW1zXEVjb3N5c3RlbSI6Mjp7czo5OiIAKgBieU5hbWUiO2E6MTE2OntzOjEwOiJicmljay9tYXRoIjtPOjIyOiJMYXJhdmVsXFJvc3RlclxQYWNrYWdlIjo3OntzOjc6IgAqAG5hbWUiO3M6MTA6ImJyaWNrL21hdGgiO3M6MTA6IgAqAHZlcnNpb24iO3M6NToiMS4wLjAiO3M6OToiACoAc291cmNlIjtFOjQzOiJMYXJhdmVsXFJvc3RlclxFbnVtc1xQYWNrYWdlU291cmNlOkNvbXBvc2VyIjtzOjY6IgAqAGRldiI7YjowO3M6OToiACoAZGlyZWN0IjtiOjA7czoxMzoiACoAY29uc3RyYWludCI7czowOiIiO3M6NzoiACoAcGF0aCI7czozOToiQzpcbGFyYWdvblx3d3dcY29taWRhXHZlbmRvclxicmlja1xtYXRoIjt9czozMToiY2FyYm9ucGhwL2NhcmJvbi1kb2N0cmluZS10eXBlcyI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjMxOiJjYXJib25waHAvY2FyYm9uLWRvY3RyaW5lLXR5cGVzIjtzOjEwOiIAKgB2ZXJzaW9uIjtzOjU6IjMuMi4xIjtzOjk6IgAqAHNvdXJjZSI7cjo4O3M6NjoiACoAZGV2IjtiOjA7czo5OiIAKgBkaXJlY3QiO2I6MDtzOjEzOiIAKgBjb25zdHJhaW50IjtzOjA6IiI7czo3OiIAKgBwYXRoIjtzOjYwOiJDOlxsYXJhZ29uXHd3d1xjb21pZGFcdmVuZG9yXGNhcmJvbnBocFxjYXJib24tZG9jdHJpbmUtdHlwZXMiO31zOjIzOiJkZmx5ZGV2L2RvdC1hY2Nlc3MtZGF0YSI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjIzOiJkZmx5ZGV2L2RvdC1hY2Nlc3MtZGF0YSI7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cG9seWZpbGwtaW50bC1ncmFwaGVtZSI7czoxMDoiACoAdmVyc2lvbiI7czo2OiIxLjQxLjAiO3M6OToiACoAc291cmNlIjtyOjg7czo2OiIAKgBkZXYiO2I6MDtzOjk6IgAqAGRpcmVjdCI7YjowO3M6MTM6IgAqAGNvbnN0cmFpbnQiO3M6MDoiIjtzOjc6IgAqAHBhdGgiO3M6NTk6IkM6XGxhcmFnb25cd3d3XGNvbWlkYVx2ZW5kb3Jcc3ltZm9ueVxwb2x5ZmlsbC1pbnRsLWdyYXBoZW1lIjt9czoyNToic3ltZm9ueS9wb2x5ZmlsbC1pbnRsLWlkbiI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjI1OiJzeW1mb255L3BvbHlmaWxsLWludGwtaWRuIjtzOjEwOiIAKgB2ZXJzaW9uIjtzOjY6IjEuNDIuMCI7czo5OiIAKgBzb3VyY2UiO3I6ODtzOjY6IgAqAGRldiI7YjowO3M6OToiACoAZGlyZWN0IjtiOjA7czoxMzoiACoAY29uc3RyYWludCI7czowOiIiO3M6NzoiACoAcGF0aCI7czo1NDoiQzpcbGFyYWdvblx3d3dcY29taWRhXHZlbmRvclxzeW1mb255XHBvbHlmaWxsLWludGwtaWRuIjt9czozMjoic3ltZm9ueS9wb2x5ZmlsbC1pbnRsLW5vcm1hbGl6ZXIiO086MjI6IkxhcmF2ZWxcUm9zdGVyXFBhY2thZ2UiOjc6e3M6NzoiACoAbmFtZSI7czozMjoic3ltZm9ueS9wb2x5ZmlsbC1pbnRsLW5vcm1hbGl6ZXIiO3M6MTA6IgAqAHZlcnNpb24iO3M6NjoiMS40Mi4wIjtzOjk6IgAqAHNvdXJjZSI7cjo4O3M6NjoiACoAZGV2IjtiOjA7czo5OiIAKgBkaXJlY3QiO2I6MDtzOjEzOiIAKgBjb25zdHJhaW50IjtzOjA6IiI7czo3OiIAKgBwYXRoIjtzOjYxOiJDOlxsYXJhZ29uXHd3d1xjb21pZGFcdmVuZG9yXHN5bWZvbnlccG9seWZpbGwtaW50bC1ub3JtYWxpemVyIjt9czoyNToic3ltZm9ueS9wb2x5ZmlsbC1tYnN0cmluZyI7TzoyMjoiTGFyYXZl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NztpOjYwO3I6NDg1O2k6NjE7cjo0OTM7aTo2MjtyOjUwMTtpOjYzO3I6NTA5O2k6NjQ7cjo1MTc7aTo2NTtyOjUyNTtpOjY2O3I6NTMzO2k6Njc7cjo1NDE7aTo2ODtyOjU0OTtpOjY5O3I6NTU3O2k6NzA7cjo1NjU7aTo3MTtyOjU3MztpOjcyO3I6NTgxO2k6NzM7cjo1ODk7aTo3NDtyOjU5NztpOjc1O3I6NjA1O2k6NzY7cjo2MTM7aTo3NztyOjYyMTtpOjc4O3I6NjI5O2k6Nzk7cjo2Mzc7aTo4MDtyOjY0NTtpOjgxO3I6NjUzO2k6ODI7cjo2NjE7aTo4MztyOjY2OTtpOjg0O3I6Njc3O2k6ODU7cjo2ODU7aTo4NjtyOjY5MztpOjg3O3I6NzAxO2k6ODg7cjo3MDk7aTo4OTtyOjcxNztpOjkwO3I6NzI1O2k6OTE7cjo3MzM7aTo5MjtyOjc0MTtpOjkzO3I6NzQ5O2k6OTQ7cjo3NTc7aTo5NTtyOjc2NTtpOjk2O3I6NzczO2k6OTc7cjo3ODE7aTo5ODtyOjc4OTtpOjk5O3I6Nzk3O2k6MTAwO3I6ODA1O2k6MTAxO3I6ODEzO2k6MTAyO3I6ODIxO2k6MTAzO3I6ODI5O2k6MTA0O3I6ODM3O2k6MTA1O3I6ODQ1O2k6MTA2O3I6ODUzO2k6MTA3O3I6ODYxO2k6MTA4O3I6ODY5O2k6MTA5O3I6ODc3O2k6MTEwO3I6ODg1O2k6MTExO3I6ODkzO2k6MTEyO3I6OTAxO2k6MTEzO3I6OTA5O2k6MTE0O3I6OTE3O2k6MTE1O3I6OTI1O31zOjI4OiIAKgBlc2NhcGVXaGVuQ2FzdGluZ1RvU3RyaW5nIjtiOjA7fX1zOjI6ImpzIjtPOjM3OiJMYXJhdmVsXFJvc3RlclxFY29zeXN0ZW1zXEpzRWNvc3lzdGVtIjozOntzOjk6IgAqAGJ5TmFtZSI7YToxNDg6e3M6MjQ6IkBhbGNhbHpvbmUvYW5zaS10b2tlbml6ZSI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjI0OiJAYWxjYWx6b25lL2Fuc2ktdG9rZW5pemUiO3M6MTA6IgAqAHZlcnNpb24iO3M6NToiMC4zLjEiO3M6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cmFnb25cd3d3XGNvbWlkYVxub2RlX21vZHVsZXNcQGpyaWRnZXdlbGxcdHJhY2UtbWFwcGluZyI7fXM6MTg6IkBveGMtcHJvamVjdC90eXBlcyI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjE4OiJAb3hjLXByb2plY3QvdHlwZXMiO3M6MTA6IgAqAHZlcnNpb24iO3M6NzoiMC4xNTEuMCI7czo5OiIAKgBzb3VyY2UiO3I6MTA1NztzOjY6IgAqAGRldiI7YjoxO3M6OToiACoAZGlyZWN0IjtiOjA7czoxMzoiACoAY29uc3RyYWludCI7czowOiIiO3M6NzoiACoAcGF0aCI7czo1MzoiQzpcbGFyYWdvblx3d3dcY29taWRhXG5vZGVfbW9kdWxlc1xAb3hjLXByb2plY3RcdHlwZXMiO31zOjM0OiJAcm9sbGRvd24vYmluZGluZy1hbmRyb2lkLWFybS1lYWJpIjtPOjIyOiJMYXJhdmVsXFJvc3RlclxQYWNrYWdlIjo3OntzOjc6IgAqAG5hbWUiO3M6MzQ6IkByb2xsZG93bi9iaW5kaW5nLWFuZHJvaWQtYXJtLWVhYmkiO3M6MTA6IgAqAHZlcnNpb24iO3M6NjoiMS4yLjExIjtzOjk6IgAqAHNvdXJjZSI7cjoxMDU3O3M6NjoiACoAZGV2IjtiOjE7czo5OiIAKgBkaXJlY3QiO2I6MDtzOjEzOiIAKgBjb25zdHJhaW50IjtzOjA6IiI7czo3OiIAKgBwYXRoIjtzOjY5OiJDOlxsYXJhZ29uXHd3d1xjb21pZGFcbm9kZV9tb2R1bGVzXEByb2xsZG93blxiaW5kaW5nLWFuZHJvaWQtYXJtLWVhYmkiO31zOjMxOiJAcm9sbGRvd24vYmluZGluZy1hbmRyb2lkLWFybTY0IjtPOjIyOiJMYXJhdmVsXFJvc3RlclxQYWNrYWdlIjo3OntzOjc6IgAqAG5hbWUiO3M6MzE6IkByb2xsZG93bi9iaW5kaW5nLWFuZHJvaWQtYXJtNjQiO3M6MTA6IgAqAHZlcnNpb24iO3M6NjoiMS4yLjExIjtzOjk6IgAqAHNvdXJjZSI7cjoxMDU3O3M6NjoiACoAZGV2IjtiOjE7czo5OiIAKgBkaXJlY3QiO2I6MDtzOjEzOiIAKgBjb25zdHJhaW50IjtzOjA6IiI7czo3OiIAKgBwYXRoIjtzOjY2OiJDOlxsYXJhZ29uXHd3d1xjb21pZGFcbm9kZV9tb2R1bGVzXEByb2xsZG93blxiaW5kaW5nLWFuZHJvaWQtYXJtNjQiO31zOjMwOiJAcm9sbGRvd24vYmluZGluZy1kYXJ3aW4tYXJtNjQiO086MjI6IkxhcmF2ZWxcUm9zdGVyXFBhY2thZ2UiOjc6e3M6NzoiACoAbmFtZSI7czozMDoiQHJvbGxkb3duL2JpbmRpbmctZGFyd2luLWFybTY0IjtzOjEwOiIAKgB2ZXJzaW9uIjtzOjY6IjEuMi4xMSI7czo5OiIAKgBzb3VyY2UiO3I6MTA1NztzOjY6IgAqAGRldiI7YjoxO3M6OToiACoAZGlyZWN0IjtiOjA7czoxMzoiACoAY29uc3RyYWludCI7czowOiIiO3M6NzoiACoAcGF0aCI7czo2NToiQzpcbGFyYWdvblx3d3dcY29taWRhXG5vZGVfbW9kdWxlc1xAcm9sbGRvd25cYmluZGluZy1kYXJ3aW4tYXJtNjQiO31zOjI4OiJAcm9sbGRvd24vYmluZGluZy1kYXJ3aW4teDY0IjtPOjIyOiJMYXJhdmVsXFJvc3RlclxQYWNrYWdlIjo3OntzOjc6IgAqAG5hbWUiO3M6Mjg6IkByb2xsZG93bi9iaW5kaW5nLWRhcndpbi14NjQiO3M6MTA6IgAqAHZlcnNpb24iO3M6NjoiMS4yLjExIjtzOjk6IgAqAHNvdXJjZSI7cjoxMDU3O3M6NjoiACoAZGV2IjtiOjE7czo5OiIAKgBkaXJlY3QiO2I6MDtzOjEzOiIAKgBjb25zdHJhaW50IjtzOjA6IiI7czo3OiIAKgBwYXRoIjtzOjYzOiJDOlxsYXJhZ29uXHd3d1xjb21pZGFcbm9kZV9tb2R1bGVzXEByb2xsZG93blxiaW5kaW5nLWRhcndpbi14NjQiO31zOjI5OiJAcm9sbGRvd24vYmluZGluZy1mcmVlYnNkLXg2NCI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjI5OiJAcm9sbGRvd24vYmluZGluZy1mcmVlYnNkLXg2NCI7czoxMDoiACoAdmVyc2lvbiI7czo2OiIxLjIuMTEiO3M6OToiACoAc291cmNlIjtyOjEwNTc7czo2OiIAKgBkZXYiO2I6MTtzOjk6IgAqAGRpcmVjdCI7YjowO3M6MTM6IgAqAGNvbnN0cmFpbnQiO3M6MDoiIjtzOjc6IgAqAHBhdGgiO3M6NjQ6IkM6XGxhcmFnb25cd3d3XGNvbWlkYVxub2RlX21vZHVsZXNcQHJvbGxkb3duXGJpbmRpbmctZnJlZWJzZC14NjQiO31zOjM3OiJAcm9sbGRvd24vYmluZGluZy1saW51eC1hcm0tZ251ZWFiaWhmIjtPOjIyOiJMYXJhdmVsXFJvc3RlclxQYWNrYWdlIjo3OntzOjc6IgAqAG5hbWUiO3M6Mzc6IkByb2xsZG93bi9iaW5kaW5nLWxpbnV4LWFybS1nbnVlYWJpaGYiO3M6MTA6IgAqAHZlcnNpb24iO3M6NjoiMS4yLjExIjtzOjk6IgAqAHNvdXJjZSI7cjoxMDU3O3M6NjoiACoAZGV2IjtiOjE7czo5OiIAKgBkaXJlY3QiO2I6MDtzOjEzOiIAKgBjb25zdHJhaW50IjtzOjA6IiI7czo3OiIAKgBwYXRoIjtzOjcyOiJDOlxsYXJhZ29uXHd3d1xjb21pZGFcbm9kZV9tb2R1bGVzXEByb2xsZG93blxiaW5kaW5nLWxpbnV4LWFybS1nbnVlYWJpaGYiO31zOjMzOiJAcm9sbGRvd24vYmluZGluZy1saW51eC1hcm02NC1nbnUiO086MjI6IkxhcmF2ZWxcUm9zdGVyXFBhY2thZ2UiOjc6e3M6NzoiACoAbmFtZSI7czozMzoiQHJvbGxkb3duL2JpbmRpbmctbGludXgtYXJtNjQtZ251IjtzOjEwOiIAKgB2ZXJzaW9uIjtzOjY6IjEuMi4xMSI7czo5OiIAKgBzb3VyY2UiO3I6MTA1NztzOjY6IgAqAGRldiI7YjoxO3M6OToiACoAZGlyZWN0IjtiOjA7czoxMzoiACoAY29uc3RyYWludCI7czowOiIiO3M6NzoiACoAcGF0aCI7czo2ODoiQzpcbGFyYWdvblx3d3dcY29taWRhXG5vZGVfbW9kdWxlc1xAcm9sbGRvd25cYmluZGluZy1saW51eC1hcm02NC1nbnUiO31zOjM0OiJAcm9sbGRvd24vYmluZGluZy1saW51eC1hcm02NC1tdXNsIjtPOjIyOiJMYXJhdmVsXFJvc3RlclxQYWNrYWdlIjo3OntzOjc6IgAqAG5hbWUiO3M6MzQ6IkByb2xsZG93bi9iaW5kaW5nLWxpbnV4LWFybTY0LW11c2wiO3M6MTA6IgAqAHZlcnNpb24iO3M6NjoiMS4yLjExIjtzOjk6IgAqAHNvdXJjZSI7cjoxMDU3O3M6NjoiACoAZGV2IjtiOjE7czo5OiIAKgBkaXJlY3QiO2I6MDtzOjEzOiIAKgBjb25zdHJhaW50IjtzOjA6IiI7czo3OiIAKgBwYXRoIjtzOjY5OiJDOlxsYXJhZ29uXHd3d1xjb21pZGFcbm9kZV9tb2R1bGVzXEByb2xsZG93blxiaW5kaW5nLWxpbnV4LWFybTY0LW11c2wiO31zOjMzOiJAcm9sbGRvd24vYmluZGluZy1saW51eC1wcGM2NC1nbnUiO086MjI6IkxhcmF2ZWxcUm9zdGVyXFBhY2thZ2UiOjc6e3M6NzoiACoAbmFtZSI7czozMzoiQHJvbGxkb3duL2JpbmRpbmctbGludXgtcHBjNjQtZ251IjtzOjEwOiIAKgB2ZXJzaW9uIjtzOjY6IjEuMi4xMSI7czo5OiIAKgBzb3VyY2UiO3I6MTA1NztzOjY6IgAqAGRldiI7YjoxO3M6OToiACoAZGlyZWN0IjtiOjA7czoxMzoiACoAY29uc3RyYWludCI7czowOiIiO3M6NzoiACoAcGF0aCI7czo2ODoiQzpcbGFyYWdvblx3d3dcY29taWRhXG5vZGVfbW9kdWxlc1xAcm9sbGRvd25cYmluZGluZy1saW51eC1wcGM2NC1nbnUiO31zOjMzOiJAcm9sbGRvd24vYmluZGluZy1saW51eC1zMzkweC1nbnUiO086MjI6IkxhcmF2ZWxcUm9zdGVyXFBhY2thZ2UiOjc6e3M6NzoiACoAbmFtZSI7czozMzoiQHJvbGxkb3duL2JpbmRpbmctbGludXgtczM5MHgtZ251IjtzOjEwOiIAKgB2ZXJzaW9uIjtzOjY6IjEuMi4xMSI7czo5OiIAKgBzb3VyY2UiO3I6MTA1NztzOjY6IgAqAGRldiI7YjoxO3M6OToiACoAZGlyZWN0IjtiOjA7czoxMzoiACoAY29uc3RyYWludCI7czowOiIiO3M6NzoiACoAcGF0aCI7czo2ODoiQzpcbGFyYWdvblx3d3dcY29taWRhXG5vZGVfbW9kdWxlc1xAcm9sbGRvd25cYmluZGluZy1saW51eC1zMzkweC1nbnUiO31zOjMxOiJAcm9sbGRvd24vYmluZGluZy1saW51eC14NjQtZ251IjtPOjIyOiJMYXJhdmVsXFJvc3RlclxQYWNrYWdlIjo3OntzOjc6IgAqAG5hbWUiO3M6MzE6IkByb2xsZG93bi9iaW5kaW5nLWxpbnV4LXg2NC1nbnUiO3M6MTA6IgAqAHZlcnNpb24iO3M6NjoiMS4yLjExIjtzOjk6IgAqAHNvdXJjZSI7cjoxMDU3O3M6NjoiACoAZGV2IjtiOjE7czo5OiIAKgBkaXJlY3QiO2I6MDtzOjEzOiIAKgBjb25zdHJhaW50IjtzOjA6IiI7czo3OiIAKgBwYXRoIjtzOjY2OiJDOlxsYXJhZ29uXHd3d1xjb21pZGFcbm9kZV9tb2R1bGVzXEByb2xsZG93blxiaW5kaW5nLWxpbnV4LXg2NC1nbnUiO31zOjMyOiJAcm9sbGRvd24vYmluZGluZy1saW51eC14NjQtbXVzbCI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjMyOiJAcm9sbGRvd24vYmluZGluZy1saW51eC14NjQtbXVzbCI7czoxMDoiACoAdmVyc2lvbiI7czo2OiIxLjIuMTEiO3M6OToiACoAc291cmNlIjtyOjEwNTc7czo2OiIAKgBkZXYiO2I6MTtzOjk6IgAqAGRpcmVjdCI7YjowO3M6MTM6IgAqAGNvbnN0cmFpbnQiO3M6MDoiIjtzOjc6IgAqAHBhdGgiO3M6Njc6IkM6XGxhcmFnb25cd3d3XGNvbWlkYVxub2RlX21vZHVsZXNcQHJvbGxkb3duXGJpbmRpbmctbGludXgteDY0LW11c2wiO31zOjM1OiJAcm9sbGRvd24vYmluZGluZy1vcGVuaGFybW9ueS1hcm02NCI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjM1OiJAcm9sbGRvd24vYmluZGluZy1vcGVuaGFybW9ueS1hcm02NCI7czoxMDoiACoAdmVyc2lvbiI7czo2OiIxLjIuMTEiO3M6OToiACoAc291cmNlIjtyOjEwNTc7czo2OiIAKgBkZXYiO2I6MTtzOjk6IgAqAGRpcmVjdCI7YjowO3M6MTM6IgAqAGNvbnN0cmFpbnQiO3M6MDoiIjtzOjc6IgAqAHBhdGgiO3M6NzA6IkM6XGxhcmFnb25cd3d3XGNvbWlkYVxub2RlX21vZHVsZXNcQHJvbGxkb3duXGJpbmRpbmctb3Blbmhhcm1vbnktYXJtNjQiO31zOjM0OiJAcm9sbGRvd24vYmluZGluZy13aW4zMi1hcm02NC1tc3ZjIjtPOjIyOiJMYXJhdmVsXFJvc3RlclxQYWNrYWdlIjo3OntzOjc6IgAqAG5hbWUiO3M6MzQ6IkByb2xsZG93bi9iaW5kaW5nLXdpbjMyLWFybTY0LW1zdmMiO3M6MTA6IgAqAHZlcnNpb24iO3M6NjoiMS4yLjExIjtzOjk6IgAqAHNvdXJjZSI7cjoxMDU3O3M6NjoiACoAZGV2IjtiOjE7czo5OiIAKgBkaXJlY3QiO2I6MDtzOjEzOiIAKgBjb25zdHJhaW50IjtzOjA6IiI7czo3OiIAKgBwYXRoIjtzOjY5OiJDOlxsYXJhZ29uXHd3d1xjb21pZGFcbm9kZV9tb2R1bGVzXEByb2xsZG93blxiaW5kaW5nLXdpbjMyLWFybTY0LW1zdmMiO31zOjMyOiJAcm9sbGRvd24vYmluZGluZy13aW4zMi14NjQtbXN2YyI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjMyOiJAcm9sbGRvd24vYmluZGluZy13aW4zMi14NjQtbXN2YyI7czoxMDoiACoAdmVyc2lvbiI7czo2OiIxLjIuMTEiO3M6OToiACoAc291cmNlIjtyOjEwNTc7czo2OiIAKgBkZXYiO2I6MTtzOjk6IgAqAGRpcmVjdCI7YjowO3M6MTM6IgAqAGNvbnN0cmFpbnQiO3M6MDoiIjtzOjc6IgAqAHBhdGgiO3M6Njc6IkM6XGxhcmFnb25cd3d3XGNvbWlkYVxub2RlX21vZHVsZXNcQHJvbGxkb3duXGJpbmRpbmctd2luMzIteDY0LW1zdmMiO31zOjIxOiJAcm9sbGRvd24vcGx1Z2ludXRpbHMiO086MjI6IkxhcmF2ZWxcUm9zdGVyXFBhY2thZ2UiOjc6e3M6NzoiACoAbmFtZSI7czoyMToiQHJvbGxkb3duL3BsdWdpbnV0aWxzIjtzOjEwOiIAKgB2ZXJzaW9uIjtzOjU6IjEuMC4xIjtzOjk6IgAqAHNvdXJjZSI7cjoxMDU3O3M6NjoiACoAZGV2IjtiOjE7czo5OiIAKgBkaXJlY3QiO2I6MDtzOjEzOiIAKgBjb25zdHJhaW50IjtzOjA6IiI7czo3OiIAKgBwYXRoIjtzOjU2OiJDOlxsYXJhZ29uXHd3d1xjb21pZGFcbm9kZV9tb2R1bGVzXEByb2xsZG93blxwbHVnaW51dGlscyI7fXM6MTc6IkB0YWlsd2luZGNzcy9ub2RlIjtPOjIyOiJMYXJhdmVsXFJvc3RlclxQYWNrYWdlIjo3OntzOjc6IgAqAG5hbWUiO3M6MTc6IkB0YWlsd2luZGNzcy9ub2RlIjtzOjEwOiIAKgB2ZXJzaW9uIjtzOjU6IjQuMy4zIjtzOjk6IgAqAHNvdXJjZSI7cjoxMDU3O3M6NjoiACoAZGV2IjtiOjE7czo5OiIAKgBkaXJlY3QiO2I6MDtzOjEzOiIAKgBjb25zdHJhaW50IjtzOjA6IiI7czo3OiIAKgBwYXRoIjtzOjUyOiJDOlxsYXJhZ29uXHd3d1xjb21pZGFcbm9kZV9tb2R1bGVzXEB0YWlsd2luZGNzc1xub2RlIjt9czoxODoiQHRhaWx3aW5kY3NzL294aWRlIjtPOjIyOiJMYXJhdmVsXFJvc3RlclxQYWNrYWdlIjo3OntzOjc6IgAqAG5hbWUiO3M6MTg6IkB0YWlsd2luZGNzcy9veGlkZSI7czoxMDoiACoAdmVyc2lvbiI7czo1OiI0LjMuMyI7czo5OiIAKgBzb3VyY2UiO3I6MTA1NztzOjY6IgAqAGRldiI7YjoxO3M6OToiACoAZGlyZWN0IjtiOjA7czoxMzoiACoAY29uc3RyYWludCI7czowOiIiO3M6NzoiACoAcGF0aCI7czo1MzoiQzpcbGFyYWdvblx3d3dcY29taWRhXG5vZGVfbW9kdWxlc1xAdGFpbHdpbmRjc3Ncb3hpZGUiO31zOjMyOiJAdGFpbHdpbmRjc3Mvb3hpZGUtYW5kcm9pZC1hcm02NCI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjMyOiJAdGFpbHdpbmRjc3Mvb3hpZGUtYW5kcm9pZC1hcm02NCI7czoxMDoiACoAdmVyc2lvbiI7czo1OiI0LjMuMyI7czo5OiIAKgBzb3VyY2UiO3I6MTA1NztzOjY6IgAqAGRldiI7YjoxO3M6OToiACoAZGlyZWN0IjtiOjA7czoxMzoiACoAY29uc3RyYWludCI7czowOiIiO3M6NzoiACoAcGF0aCI7czo2NzoiQzpcbGFyYWdvblx3d3dcY29taWRhXG5vZGVfbW9kdWxlc1xAdGFpbHdpbmRjc3Ncb3hpZGUtYW5kcm9pZC1hcm02NCI7fXM6MzE6IkB0YWlsd2luZGNzcy9veGlkZS1kYXJ3aW4tYXJtNjQiO086MjI6IkxhcmF2ZWxcUm9zdGVyXFBhY2thZ2UiOjc6e3M6NzoiACoAbmFtZSI7czozMToiQHRhaWx3aW5kY3NzL294aWRlLWRhcndpbi1hcm02NCI7czoxMDoiACoAdmVyc2lvbiI7czo1OiI0LjMuMyI7czo5OiIAKgBzb3VyY2UiO3I6MTA1NztzOjY6IgAqAGRldiI7YjoxO3M6OToiACoAZGlyZWN0IjtiOjA7czoxMzoiACoAY29uc3RyYWludCI7czowOiIiO3M6NzoiACoAcGF0aCI7czo2NjoiQzpcbGFyYWdvblx3d3dcY29taWRhXG5vZGVfbW9kdWxlc1xAdGFpbHdpbmRjc3Ncb3hpZGUtZGFyd2luLWFybTY0Ijt9czoyOToiQHRhaWx3aW5kY3NzL294aWRlLWRhcndpbi14NjQiO086MjI6IkxhcmF2ZWxcUm9zdGVyXFBhY2thZ2UiOjc6e3M6NzoiACoAbmFtZSI7czoyOToiQHRhaWx3aW5kY3NzL294aWRlLWRhcndpbi14NjQiO3M6MTA6IgAqAHZlcnNpb24iO3M6NToiNC4zLjMiO3M6OToiACoAc291cmNlIjtyOjEwNTc7czo2OiIAKgBkZXYiO2I6MTtzOjk6IgAqAGRpcmVjdCI7YjowO3M6MTM6IgAqAGNvbnN0cmFpbnQiO3M6MDoiIjtzOjc6IgAqAHBhdGgiO3M6NjQ6IkM6XGxhcmFnb25cd3d3XGNvbWlkYVxub2RlX21vZHVsZXNcQHRhaWx3aW5kY3NzXG94aWRlLWRhcndpbi14NjQiO31zOjMwOiJAdGFpbHdpbmRjc3Mvb3hpZGUtZnJlZWJzZC14NjQiO086MjI6IkxhcmF2ZWxcUm9zdGVyXFBhY2thZ2UiOjc6e3M6NzoiACoAbmFtZSI7czozMDoiQHRhaWx3aW5kY3NzL294aWRlLWZyZWVic2QteDY0IjtzOjEwOiIAKgB2ZXJzaW9uIjtzOjU6IjQuMy4zIjtzOjk6IgAqAHNvdXJjZSI7cjoxMDU3O3M6NjoiACoAZGV2IjtiOjE7czo5OiIAKgBkaXJlY3QiO2I6MDtzOjEzOiIAKgBjb25zdHJhaW50IjtzOjA6IiI7czo3OiIAKgBwYXRoIjtzOjY1OiJDOlxsYXJhZ29uXHd3d1xjb21pZGFcbm9kZV9tb2R1bGVzXEB0YWlsd2luZGNzc1xveGlkZS1mcmVlYnNkLXg2NCI7fXM6Mzg6IkB0YWlsd2luZGNzcy9veGlkZS1saW51eC1hcm0tZ251ZWFiaWhmIjtPOjIyOiJMYXJhdmVsXFJvc3RlclxQYWNrYWdlIjo3OntzOjc6IgAqAG5hbWUiO3M6Mzg6IkB0YWlsd2luZGNzcy9veGlkZS1saW51eC1hcm0tZ251ZWFiaWhmIjtzOjEwOiIAKgB2ZXJzaW9uIjtzOjU6IjQuMy4zIjtzOjk6IgAqAHNvdXJjZSI7cjoxMDU3O3M6NjoiACoAZGV2IjtiOjE7czo5OiIAKgBkaXJlY3QiO2I6MDtzOjEzOiIAKgBjb25zdHJhaW50IjtzOjA6IiI7czo3OiIAKgBwYXRoIjtzOjczOiJDOlxsYXJhZ29uXHd3d1xjb21pZGFcbm9kZV9tb2R1bGVzXEB0YWlsd2luZGNzc1xveGlkZS1saW51eC1hcm0tZ251ZWFiaWhmIjt9czozNDoiQHRhaWx3aW5kY3NzL294aWRlLWxpbnV4LWFybTY0LWdudSI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjM0OiJAdGFpbHdpbmRjc3Mvb3hpZGUtbGludXgtYXJtNjQtZ251IjtzOjEwOiIAKgB2ZXJzaW9uIjtzOjU6IjQuMy4zIjtzOjk6IgAqAHNvdXJjZSI7cjoxMDU3O3M6NjoiACoAZGV2IjtiOjE7czo5OiIAKgBkaXJlY3QiO2I6MDtzOjEzOiIAKgBjb25zdHJhaW50IjtzOjA6IiI7czo3OiIAKgBwYXRoIjtzOjY5OiJDOlxsYXJhZ29uXHd3d1xjb21pZGFcbm9kZV9tb2R1bGVzXEB0YWlsd2luZGNzc1xveGlkZS1saW51eC1hcm02NC1nbnUiO31zOjM1OiJAdGFpbHdpbmRjc3Mvb3hpZGUtbGludXgtYXJtNjQtbXVzbCI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjM1OiJAdGFpbHdpbmRjc3Mvb3hpZGUtbGludXgtYXJtNjQtbXVzbCI7czoxMDoiACoAdmVyc2lvbiI7czo1OiI0LjMuMyI7czo5OiIAKgBzb3VyY2UiO3I6MTA1NztzOjY6IgAqAGRldiI7YjoxO3M6OToiACoAZGlyZWN0IjtiOjA7czoxMzoiACoAY29uc3RyYWludCI7czowOiIiO3M6NzoiACoAcGF0aCI7czo3MDoiQzpcbGFyYWdvblx3d3dcY29taWRhXG5vZGVfbW9kdWxlc1xAdGFpbHdpbmRjc3Ncb3hpZGUtbGludXgtYXJtNjQtbXVzbCI7fXM6MzI6IkB0YWlsd2luZGNzcy9veGlkZS1saW51eC14NjQtZ251IjtPOjIyOiJMYXJhdmVsXFJvc3RlclxQYWNrYWdlIjo3OntzOjc6IgAqAG5hbWUiO3M6MzI6IkB0YWlsd2luZGNzcy9veGlkZS1saW51eC14NjQtZ251IjtzOjEwOiIAKgB2ZXJzaW9uIjtzOjU6IjQuMy4zIjtzOjk6IgAqAHNvdXJjZSI7cjoxMDU3O3M6NjoiACoAZGV2IjtiOjE7czo5OiIAKgBkaXJlY3QiO2I6MDtzOjEzOiIAKgBjb25zdHJhaW50IjtzOjA6IiI7czo3OiIAKgBwYXRoIjtzOjY3OiJDOlxsYXJhZ29uXHd3d1xjb21pZGFcbm9kZV9tb2R1bGVzXEB0YWlsd2luZGNzc1xveGlkZS1saW51eC14NjQtZ251Ijt9czozMzoiQHRhaWx3aW5kY3NzL294aWRlLWxpbnV4LXg2NC1tdXNsIjtPOjIyOiJMYXJhdmVsXFJvc3RlclxQYWNrYWdlIjo3OntzOjc6IgAqAG5hbWUiO3M6MzM6IkB0YWlsd2luZGNzcy9veGlkZS1saW51eC14NjQtbXVzbCI7czoxMDoiACoAdmVyc2lvbiI7czo1OiI0LjMuMyI7czo5OiIAKgBzb3VyY2UiO3I6MTA1NztzOjY6IgAqAGRldiI7YjoxO3M6OToiACoAZGlyZWN0IjtiOjA7czoxMzoiACoAY29uc3RyYWludCI7czowOiIiO3M6NzoiACoAcGF0aCI7czo2ODoiQzpcbGFyYWdvblx3d3dcY29taWRhXG5vZGVfbW9kdWxlc1xAdGFpbHdpbmRjc3Ncb3hpZGUtbGludXgteDY0LW11c2wiO31zOjMwOiJAdGFpbHdpbmRjc3Mvb3hpZGUtd2FzbTMyLXdhc2kiO086MjI6IkxhcmF2ZWxcUm9zdGVyXFBhY2thZ2UiOjc6e3M6NzoiACoAbmFtZSI7czozMDoiQHRhaWx3aW5kY3NzL294aWRlLXdhc20zMi13YXNpIjtzOjEwOiIAKgB2ZXJzaW9uIjtzOjU6IjQuMy4zIjtzOjk6IgAqAHNvdXJjZSI7cjoxMDU3O3M6NjoiACoAZGV2IjtiOjE7czo5OiIAKgBkaXJlY3QiO2I6MDtzOjEzOiIAKgBjb25zdHJhaW50IjtzOjA6IiI7czo3OiIAKgBwYXRoIjtzOjY1OiJDOlxsYXJhZ29uXHd3d1xjb21pZGFcbm9kZV9tb2R1bGVzXEB0YWlsd2luZGNzc1xveGlkZS13YXNtMzItd2FzaSI7fXM6MzU6IkB0YWlsd2luZGNzcy9veGlkZS13aW4zMi1hcm02NC1tc3ZjIjtPOjIyOiJMYXJhdmVsXFJvc3RlclxQYWNrYWdlIjo3OntzOjc6IgAqAG5hbWUiO3M6MzU6IkB0YWlsd2luZGNzcy9veGlkZS13aW4zMi1hcm02NC1tc3ZjIjtzOjEwOiIAKgB2ZXJzaW9uIjtzOjU6IjQuMy4zIjtzOjk6IgAqAHNvdXJjZSI7cjoxMDU3O3M6NjoiACoAZGV2IjtiOjE7czo5OiIAKgBkaXJlY3QiO2I6MDtzOjEzOiIAKgBjb25zdHJhaW50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O3M6MTM6IgAqAGNvbnN0cmFpbnQiO3M6MDoiIjtzOjc6IgAqAHBhdGgiO3M6NTk6IkM6XGxhcmFnb25cd3d3XGNvbWlkYVxub2RlX21vZHVsZXNcbGlnaHRuaW5nY3NzLWZyZWVic2QteDY0Ijt9czozMjoibGlnaHRuaW5nY3NzLWxpbnV4LWFybS1nbnVlYWJpaGYiO086MjI6IkxhcmF2ZWxcUm9zdGVyXFBhY2thZ2UiOjc6e3M6NzoiACoAbmFtZSI7czozMjoibGlnaHRuaW5nY3NzLWxpbnV4LWFybS1nbnVlYWJpaGYiO3M6MTA6IgAqAHZlcnNpb24iO3M6NjoiMS4zMi4wIjtzOjk6IgAqAHNvdXJjZSI7cjoxMDU3O3M6NjoiACoAZGV2IjtiOjE7czo5OiIAKgBkaXJlY3QiO2I6MDtzOjEzOiIAKgBjb25zdHJhaW50IjtzOjA6IiI7czo3OiIAKgBwYXRoIjtzOjY3OiJDOlxsYXJhZ29uXHd3d1xjb21pZGFcbm9kZV9tb2R1bGVzXGxpZ2h0bmluZ2Nzcy1saW51eC1hcm0tZ251ZWFiaWhmIjt9czoyODoibGlnaHRuaW5nY3NzLWxpbnV4LWFybTY0LWdudSI7TzoyMjoiTGFyYXZlbFxSb3N0ZXJcUGFja2FnZSI6Nzp7czo3OiIAKgBuYW1lIjtzOjI4OiJsaWdodG5pbmdjc3MtbGludXgtYXJtNjQtZ251IjtzOjEwOiIAKgB2ZXJzaW9uIjtzOjY6IjEuMzIuMCI7czo5OiIAKgBzb3VyY2UiO3I6MTA1NztzOjY6IgAqAGRldiI7YjoxO3M6OToiACoAZGlyZWN0IjtiOjA7czoxMzoiACoAY29uc3RyYWludCI7czowOiIiO3M6NzoiACoAcGF0aCI7czo2MzoiQzpcbGFyYWdvblx3d3dcY29taWRhXG5vZGVfbW9kdWxlc1xsaWdodG5pbmdjc3MtbGludXgtYXJtNjQtZ251Ijt9czoyOToibGlnaHRuaW5nY3NzLWxpbnV4LWFybTY0LW11c2wiO086MjI6IkxhcmF2ZWxcUm9zdGVyXFBhY2thZ2UiOjc6e3M6NzoiACoAbmFtZSI7czoyOToibGlnaHRuaW5nY3NzLWxpbnV4LWFybTY0LW11c2wiO3M6MTA6IgAqAHZlcnNpb24iO3M6NjoiMS4zMi4wIjtzOjk6IgAqAHNvdXJjZSI7cjoxMDU3O3M6NjoiACoAZGV2IjtiOjE7czo5OiIAKgBkaXJlY3QiO2I6MDtzOjEzOiIAKgBjb25zdHJhaW50IjtzOjA6IiI7czo3OiIAKgBwYXRoIjtzOjY0OiJDOlxsYXJhZ29uXHd3d1xjb21pZGFcbm9kZV9tb2R1bGVzXGxpZ2h0bmluZ2Nzcy1saW51eC1hcm02NC1tdXNsIjt9czoyNjoibGlnaHRuaW5nY3NzLWxpbnV4LXg2NC1nbnUiO086MjI6IkxhcmF2ZWxcUm9zdGVyXFBhY2thZ2UiOjc6e3M6NzoiACoAbmFtZSI7czoyNjoibGlnaHRuaW5nY3NzLWxpbnV4LXg2NC1nbnUiO3M6MTA6IgAqAHZlcnNpb24iO3M6NjoiMS4zMi4wIjtzOjk6IgAqAHNvdXJjZSI7cjoxMDU3O3M6NjoiACoAZGV2IjtiOjE7czo5OiIAKgBkaXJlY3QiO2I6MDtzOjEzOiIAKgBjb25zdHJhaW50IjtzOjA6IiI7czo3OiIAKgBwYXRoIjtzOjYxOiJDOlxsYXJhZ29uXHd3d1xjb21pZGFcbm9kZV9tb2R1bGVzXGxpZ2h0bmluZ2Nzcy1saW51eC14NjQtZ251Ijt9czoyNzoibGlnaHRuaW5nY3NzLWxpbnV4LXg2NC1tdXNsIjtPOjIyOiJMYXJhdmVsXFJvc3RlclxQYWNrYWdlIjo3OntzOjc6IgAqAG5hbWUiO3M6Mjc6ImxpZ2h0bmluZ2Nzcy1saW51eC14NjQtbXVzbCI7czoxMDoiACoAdmVyc2lvbiI7czo2OiIxLjMyLjAiO3M6OToiACoAc291cmNlIjtyOjEwNTc7czo2OiIAKgBkZXYiO2I6MTtzOjk6IgAqAGRpcmVjdCI7YjowO3M6MTM6IgAqAGNvbnN0cmFpbnQiO3M6MDoiIjtzOjc6IgAqAHBhdGgiO3M6NjI6IkM6XGxhcmFnb25cd3d3XGNvbWlkYVxub2RlX21vZHVsZXNcbGlnaHRuaW5nY3NzLWxpbnV4LXg2NC1tdXNsIjt9czoyOToibGlnaHRuaW5nY3NzLXdpbjMyLWFybTY0LW1zdmMiO086MjI6IkxhcmF2ZWxcUm9zdGVyXFBhY2th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NTQ7aToyNjtyOjEyNjI7aToyNztyOjEyNzA7aToyODtyOjEyNzg7aToyOTtyOjEyODY7aTozMDtyOjEyOTQ7aTozMTtyOjEzMDI7aTozMjtyOjEzMTA7aTozMztyOjEzMTg7aTozNDtyOjEzMjY7aTozNTtyOjEzMzQ7aTozNjtyOjEzNDI7aTozNztyOjEzNTA7aTozODtyOjEzNTg7aTozOTtyOjEzNjY7aTo0MDtyOjEzNzQ7aTo0MTtyOjEzODI7aTo0MjtyOjEzOTA7aTo0MztyOjEzOTg7aTo0NDtyOjE0MDY7aTo0NTtyOjE0MTQ7aTo0NjtyOjE0MjI7aTo0NztyOjE0MzA7aTo0ODtyOjE0Mzg7aTo0OTtyOjE0NDY7aTo1MDtyOjE0NTQ7aTo1MTtyOjE0NjI7aTo1MjtyOjE0NzA7aTo1MztyOjE0Nzg7aTo1NDtyOjE0ODY7aTo1NTtyOjE0OTQ7aTo1NjtyOjE1MDI7aTo1NztyOjE1MTA7aTo1ODtyOjE1MTg7aTo1OTtyOjE1MjY7aTo2MDtyOjE1MzQ7aTo2MTtyOjE1NDI7aTo2MjtyOjE1NTA7aTo2MztyOjE1NTg7aTo2NDtyOjE1NjY7aTo2NTtyOjE1NzQ7aTo2NjtyOjE1ODI7aTo2NztyOjE1OTA7aTo2ODtyOjE1OTg7aTo2OTtyOjE2MDY7aTo3MDtyOjE2MTQ7aTo3MTtyOjE2MjI7aTo3MjtyOjE2MzA7aTo3MztyOjE2Mzg7aTo3NDtyOjE2NDY7aTo3NTtyOjE2NTQ7aTo3NjtyOjE2NjI7aTo3NztyOjE2NzA7aTo3ODtyOjE2Nzg7aTo3OTtyOjE2ODY7aTo4MDtyOjE2OTQ7aTo4MTtyOjE3MDI7aTo4MjtyOjE3MTA7aTo4MztyOjE3MTg7aTo4NDtyOjE3MjY7aTo4NTtyOjE3MzQ7aTo4NjtyOjE3NDI7aTo4NztyOjE3NTA7aTo4ODtyOjE3NTg7aTo4OTtyOjE3NjY7aTo5MDtyOjE3NzQ7aTo5MTtyOjE3ODI7aTo5MjtyOjE3OTA7aTo5MztyOjE3OTg7aTo5NDtyOjE4MDY7aTo5NTtyOjE4MTQ7aTo5NjtyOjE4MjI7aTo5NztyOjE4MzA7aTo5ODtyOjE4Mzg7aTo5OTtyOjE4NDY7aToxMDA7cjoxODU0O2k6MTAxO3I6MTg2MjtpOjEwMjtyOjE4NzA7aToxMDM7cjoxODc4O2k6MTA0O3I6MTg4NjtpOjEwNTtyOjE4OTQ7aToxMDY7cjoxOTAyO2k6MTA3O3I6MTkxMDtpOjEwODtyOjE5MTg7aToxMDk7cjoxOTI2O2k6MTEwO3I6MTkzNDtpOjExMTtyOjE5NDI7aToxMTI7cjoxOTUwO2k6MTEzO3I6MTk1ODtpOjExNDtyOjE5NjY7aToxMTU7cjoxOTc0O2k6MTE2O3I6MTk4MjtpOjExNztyOjE5OTA7aToxMTg7cjoxOTk4O2k6MTE5O3I6MjAwNjtpOjEyMDtyOjIwMTQ7aToxMjE7cjoyMDIyO2k6MTIyO3I6MjAzMDtpOjEyMztyOjIwMzg7aToxMjQ7cjoyMDQ2O2k6MTI1O3I6MjA1NDtpOjEyNjtyOjIwNjI7aToxMjc7cjoyMDcwO2k6MTI4O3I6MjA3ODtpOjEyOTtyOjIwODY7aToxMzA7cjoyMDk0O2k6MTMxO3I6MjEwMjtpOjEzMjtyOjIxMTA7aToxMzM7cjoyMTE4O2k6MTM0O3I6MjEyNjtpOjEzNTtyOjIxMzQ7aToxMzY7cjoyMTQyO2k6MTM3O3I6MjE1MDtpOjEzODtyOjIxNTg7aToxMzk7cjoyMTY2O2k6MTQwO3I6MjE3NDtpOjE0MTtyOjIxODI7aToxNDI7cjoyMTkwO2k6MTQzO3I6MjE5ODtpOjE0NDtyOjIyMDY7aToxNDU7cjoyMjE0O2k6MTQ2O3I6MjIyMjtpOjE0NztyOjIyMzA7fXM6Mjg6IgAqAGVzY2FwZVdoZW5DYXN0aW5nVG9TdHJpbmciO2I6MDt9czoxNzoiACoAcGFja2FnZU1hbmFnZXIiO0U6NDE6IkxhcmF2ZWxcUm9zdGVyXEVudW1zXEpzUGFja2FnZU1hbmFnZXI6TnBtIjt9czo2OiJzdGFja3MiO086MzA6IkxhcmF2ZWxcUm9zdGVyXFN1cHBvcnRcRW51bVNldCI6MTp7czo4OiIAKgBjYXNlcyI7YToxOntpOjA7RTozMDoiTGFyYXZlbFxSb3N0ZXJcRW51bXNcU3RhY2s6QXBpIjt9fXM6MjE6ImJyb3dzZXJUZXN0RnJhbWV3b3JrcyI7TzozMDoiTGFyYXZlbFxSb3N0ZXJcU3VwcG9ydFxFbnVtU2V0IjoxOntzOjg6IgAqAGNhc2VzIjthOjA6e319czo5OiJmcm9udGVuZHMiO086MzA6IkxhcmF2ZWxcUm9zdGVyXFN1cHBvcnRcRW51bVNldCI6MTp7czo4OiIAKgBjYXNlcyI7YTowOnt9fXM6NjoiYWdlbnRzIjtPOjMwOiJMYXJhdmVsXFJvc3RlclxTdXBwb3J0XEVudW1TZXQiOjE6e3M6ODoiACoAY2FzZXMiO2E6Mjp7aTowO0U6Mzc6IkxhcmF2ZWxcUm9zdGVyXEVudW1zXEFnZW50OkNsYXVkZUNvZGUiO2k6MTtFOjMyOiJMYXJhdmVsXFJvc3RlclxFbnVtc1xBZ2VudDpDb2RleCI7fX1zOjc6ImVkaXRvcnMiO086MzA6IkxhcmF2ZWxcUm9zdGVyXFN1cHBvcnRcRW51bVNldCI6MTp7czo4OiIAKgBjYXNlcyI7YTowOnt9fX0=	1791490383
sazon-cache-356a192b7913b04c54574d18c28d46e6395428ab:timer	i:1791561722;	1791561722
sazon-cache-356a192b7913b04c54574d18c28d46e6395428ab	i:1;	1791561722
\.


--
-- TOC entry 5316 (class 0 OID 105964)
-- Dependencies: 264
-- Data for Name: cache_locks; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.cache_locks (key, owner, expiration) FROM stdin;
\.


--
-- TOC entry 5279 (class 0 OID 105595)
-- Dependencies: 227
-- Data for Name: categorias_ingrediente; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.categorias_ingrediente (cod_categoria_ingrediente, nom_cat, des_cat, ico_cat, act_cat, fec_cre_cat) FROM stdin;
1	Verduras	Hortalizas y verduras frescas	\N	t	2026-09-29 16:36:37
2	Frutas	Frutas frescas de estacion	\N	t	2026-09-29 16:36:37
3	Tuberculos	Papa, camote, yuca, oca y similares	\N	t	2026-09-29 16:36:37
4	Carnes rojas	Res, cerdo, cordero y llama	\N	t	2026-09-29 16:36:37
5	Aves	Pollo, pavo y otras aves	\N	t	2026-09-29 16:36:37
6	Pescados y mariscos	Pescados de rio, de mar y mariscos	\N	t	2026-09-29 16:36:37
7	Lacteos	Leche, yogur, mantequilla y quesos	\N	t	2026-09-29 16:36:37
8	Huevos	Huevos de gallina y codorniz	\N	t	2026-09-29 16:36:37
9	Cereales y granos	Arroz, quinua, trigo, maiz y avena	\N	t	2026-09-29 16:36:37
10	Legumbres	Frejol, lenteja, haba, arveja y garbanzo	\N	t	2026-09-29 16:36:37
11	Frutos secos y semillas	Mani, nueces, almendras y semillas	\N	t	2026-09-29 16:36:37
12	Aceites y grasas	Aceites vegetales, manteca y margarina	\N	t	2026-09-29 16:36:37
13	Condimentos y especias	Sal, pimienta, comino, aji y especias	\N	t	2026-09-29 16:36:37
14	Hierbas	Hierbas frescas y secas para sazonar	\N	t	2026-09-29 16:36:37
15	Endulzantes	Azucar, miel, panela y edulcorantes	\N	t	2026-09-29 16:36:37
16	Harinas y panaderia	Harinas, levaduras, pan y masas	\N	t	2026-09-29 16:36:37
17	Salsas y aderezos	Salsas envasadas, vinagres y aderezos	\N	t	2026-09-29 16:36:37
18	Enlatados y conservas	Productos enlatados y en conserva	\N	t	2026-09-29 16:36:37
19	Congelados	Productos congelados listos para usar	\N	t	2026-09-29 16:36:37
20	Bebidas	Agua, jugos, infusiones y bebidas	\N	t	2026-09-29 16:36:37
\.


--
-- TOC entry 5285 (class 0 OID 105642)
-- Dependencies: 233
-- Data for Name: categorias_receta; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.categorias_receta (cod_categoria_receta, nom_car, des_car, ico_car, act_car, fec_cre_car) FROM stdin;
1	Desayunos	Preparaciones para comenzar el dia	\N	t	2026-09-29 16:36:37
2	Almuerzos	Platos para el almuerzo	\N	t	2026-09-29 16:36:37
3	Cenas	Preparaciones ligeras para la noche	\N	t	2026-09-29 16:36:37
4	Sopas y caldos	Sopas, cremas y caldos	\N	t	2026-09-29 16:36:37
5	Ensaladas	Ensaladas frias y tibias	\N	t	2026-09-29 16:36:37
6	Platos principales	Platos de fondo con carne, ave, pescado o vegetales	\N	t	2026-09-29 16:36:37
7	Guarniciones	Acompanamientos y contornos	\N	t	2026-09-29 16:36:37
8	Comida boliviana	Platos tradicionales bolivianos	\N	t	2026-09-29 16:36:37
9	Postres	Dulces y postres	\N	t	2026-09-29 16:36:37
10	Panaderia y reposteria	Panes, tortas, galletas y masas	\N	t	2026-09-29 16:36:37
11	Snacks y aperitivos	Bocaditos y entradas	\N	t	2026-09-29 16:36:37
12	Bebidas	Refrescos, jugos, infusiones y batidos	\N	t	2026-09-29 16:36:37
13	Salsas y aderezos	Salsas, llajuas y aderezos que acompañan otros platos	\N	t	2026-09-29 22:59:49
\.


--
-- TOC entry 5287 (class 0 OID 105655)
-- Dependencies: 235
-- Data for Name: etiquetas; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.etiquetas (cod_etiqueta, nom_eti, col_eti, act_eti) FROM stdin;
1	Saludable	#16A34A	t
2	Rapido	#F59E0B	t
3	Economico	#0EA5E9	t
4	Facil	#22C55E	t
5	Vegetariano	#65A30D	t
6	Vegano	#4D7C0F	t
7	Sin gluten	#A855F7	t
8	Sin lactosa	#8B5CF6	t
9	Alto en proteinas	#DC2626	t
10	Bajo en calorias	#06B6D4	t
11	Para ninos	#EC4899	t
12	Tradicional	#B45309	t
13	Picante	#EF4444	t
14	Para llevar	#64748B	t
15	De temporada	#14B8A6	t
16	Reconfortante	#F97316	t
17	Dulce	#F472B6	t
18	Familiar	#38BDF8	t
\.


--
-- TOC entry 5320 (class 0 OID 105982)
-- Dependencies: 268
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.failed_jobs (id, uuid, connection, queue, payload, exception, failed_at) FROM stdin;
\.


--
-- TOC entry 5304 (class 0 OID 105861)
-- Dependencies: 252
-- Data for Name: favoritos; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.favoritos (cod_favorito, cod_usuario_fav, cod_receta_fav, fec_cre_fav) FROM stdin;
\.


--
-- TOC entry 5298 (class 0 OID 105797)
-- Dependencies: 246
-- Data for Name: historial_consulta; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.historial_consulta (cod_consulta, cod_usuario_con, ing_json_con, fil_json_con, tex_lib_con, tot_res_con, tie_res_con, fec_cre_con) FROM stdin;
1	1	[{"id": 19, "nombre": "Coliflor", "unidad": "pz", "cantidad": 2}]	{"nivel": "facil", "categoria": "1", "ingredientes": ["19"], "solo_completas": "1"}	\N	0	29	2026-09-30 08:43:31
2	1	[{"id": 19, "nombre": "Coliflor", "unidad": "pz", "cantidad": 2}]	{"nivel": "facil", "categoria": "5", "etiquetas": ["4"], "ingredientes": ["19"], "preferencias": [6], "respetar_dieta": "1", "solo_completas": "1"}	\N	0	21	2026-09-30 08:45:01
3	1	[{"id": 19, "nombre": "Coliflor", "unidad": "pz", "cantidad": 2}]	{"ingredientes": ["19"], "preferencias": [6], "respetar_dieta": "1"}	\N	0	34	2026-10-01 19:17:58
4	1	[{"id": 19, "nombre": "Coliflor", "unidad": "pz", "cantidad": 2}]	{"ingredientes": ["19"], "preferencias": [6], "respetar_dieta": "1"}	\N	0	42	2026-10-01 19:21:54
5	1	[{"id": 19, "nombre": "Coliflor", "unidad": "pz", "cantidad": 2}]	{"ingredientes": ["19"], "preferencias": [6], "respetar_dieta": "1"}	\N	0	47	2026-10-01 19:26:03
6	1	[{"id": 19, "nombre": "Coliflor", "unidad": "pz", "cantidad": 2}]	{"ingredientes": ["19"], "preferencias": [6], "respetar_dieta": "1"}	\N	0	40	2026-10-01 19:27:13
7	3	[{"id": 10, "nombre": "Acelga", "unidad": "@", "cantidad": 500}, {"id": 1, "nombre": "Cebolla", "unidad": "@", "cantidad": 500}, {"id": 123, "nombre": "Vino tinto", "unidad": "@", "cantidad": 500}]	{"ingredientes": ["10", "1", "123"]}	\N	1	27	2026-10-01 19:37:22
8	1	[{"id": 19, "nombre": "Coliflor", "unidad": "pz", "cantidad": 2}]	{"ingredientes": ["19"], "preferencias": [6], "respetar_dieta": "1"}	\N	0	29	2026-10-06 22:07:00
9	1	[{"id": 19, "nombre": "Coliflor", "unidad": "pz", "cantidad": 2}]	{"nivel": "dificil", "tiempo_max": 5, "solo_completas": 1, "solo_verificadas": 1}	\N	0	61	2026-10-06 22:17:34
10	1	[{"id": 19, "nombre": "Coliflor", "unidad": "pz", "cantidad": 2}]	{"nivel": "dificil", "tiempo_max": 5, "solo_completas": 1, "solo_verificadas": 1}	\N	0	42	2026-10-06 22:19:04
11	1	[{"id": 19, "nombre": "Coliflor", "unidad": "pz", "cantidad": 2}]	{"ingredientes": ["19"], "preferencias": [6], "respetar_dieta": "1"}	\N	0	89	2026-10-06 22:28:54
12	1	[{"id": 19, "nombre": "Coliflor", "unidad": "pz", "cantidad": 2}]	{"nivel": "dificil", "tiempo_max": 5, "solo_completas": 1, "solo_verificadas": 1}	\N	0	90	2026-10-06 22:39:12
13	1	[{"id": 19, "nombre": "Coliflor", "unidad": "pz", "cantidad": 2}, {"id": 20, "nombre": "Remolacha", "unidad": "pz", "cantidad": 2}]	{"ingredientes": ["19", "20"], "preferencias": [6], "respetar_dieta": "1"}	\N	0	59	2026-10-06 22:43:48
14	3	[{"id": 51, "nombre": "Pechuga de pollo", "unidad": "pz", "cantidad": 2}, {"id": 7, "nombre": "Pimiento morrón", "unidad": "pz", "cantidad": 2}]	{"nivel": "medio", "categoria": "2", "ingredientes": ["51", "7"], "solo_completas": "1"}	\N	0	34	2026-10-07 15:15:37
15	1	[{"id": 19, "nombre": "Coliflor", "unidad": "pz", "cantidad": 2}, {"id": 20, "nombre": "Remolacha", "unidad": "pz", "cantidad": 2}]	{"ingredientes": ["19", "20"], "preferencias": [6], "respetar_dieta": "1"}	\N	0	35	2026-10-09 12:02:00
\.


--
-- TOC entry 5300 (class 0 OID 105814)
-- Dependencies: 248
-- Data for Name: historial_resultado; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.historial_resultado (cod_resultado, cod_consulta_res, cod_receta_res, pos_res, sco_res, fal_json_res, sel_res, fec_cre_res) FROM stdin;
1	7	27	1	16.67	["Aceite vegetal", "Cebolla", "Huevo de gallina", "Pimienta negra", "Sal"]	f	2026-10-01 19:37:22
\.


--
-- TOC entry 5283 (class 0 OID 105622)
-- Dependencies: 231
-- Data for Name: ingredientes; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.ingredientes (cod_ingrediente, cod_categoria_ingrediente_ing, nom_ing, sin_ing, cal_ing, pro_ing, car_ing, gra_ing, ale_ing, img_ing, act_ing, fec_cre_ing, fec_act_ing, den_ing, pre_ing) FROM stdin;
1	1	Cebolla	cebolla blanca,cebolla cabeza	40.00	1.10	9.30	0.10	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
2	1	Cebolla verde	cebollín,cebolla de verdeo	32.00	1.80	7.30	0.20	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
3	1	Tomate	jitomate	18.00	0.90	3.90	0.20	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
4	1	Zanahoria	\N	41.00	0.90	9.60	0.20	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
5	1	Locoto	rocoto,ají picante	40.00	1.90	8.80	0.40	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
7	1	Pimiento morrón	pimentón,ají dulce	31.00	1.00	6.00	0.30	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
8	1	Lechuga	\N	15.00	1.40	2.90	0.20	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
9	1	Repollo	col	25.00	1.30	5.80	0.10	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
11	1	Espinaca	\N	23.00	2.90	3.60	0.40	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
12	1	Zapallo	calabaza	26.00	1.00	6.50	0.10	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
13	1	Zapallito italiano	zucchini,calabacín	17.00	1.20	3.10	0.30	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
14	1	Pepino	\N	15.00	0.70	3.60	0.10	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
15	1	Choclo	maíz tierno,elote	86.00	3.30	19.00	1.20	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
16	1	Vainita	judía verde,poroto verde	31.00	1.80	7.00	0.20	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
18	1	Brócoli	\N	34.00	2.80	6.60	0.40	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
19	1	Coliflor	\N	25.00	1.90	5.00	0.30	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
20	1	Remolacha	betarraga,beterraga	43.00	1.60	9.60	0.20	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
21	2	Limón	limón sutil	29.00	1.10	9.30	0.30	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
22	2	Naranja	\N	47.00	0.90	11.80	0.10	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
23	2	Plátano	banana,guineo	89.00	1.10	22.80	0.30	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
24	2	Manzana	\N	52.00	0.30	13.80	0.20	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
25	2	Palta	aguacate	160.00	2.00	8.50	14.70	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
26	2	Piña	ananá	50.00	0.50	13.10	0.10	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
27	2	Papaya	\N	43.00	0.50	10.80	0.30	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
28	2	Mango	\N	60.00	0.80	15.00	0.40	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
29	2	Durazno	melocotón	39.00	0.90	9.50	0.30	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
30	2	Frutilla	fresa	32.00	0.70	7.70	0.30	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
31	2	Maracuyá	\N	97.00	2.20	23.40	0.70	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
32	2	Tumbo	curuba	25.00	0.60	6.30	0.10	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
33	3	Papa	patata,papa blanca	77.00	2.00	17.50	0.10	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
34	3	Papa imilla	imilla negra	77.00	2.00	17.50	0.10	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
35	3	Chuño	papa deshidratada	333.00	4.00	80.00	0.40	f	\N	t	2026-09-29 22:59:49	\N	0.700	\N
36	3	Tunta	chuño blanco	323.00	2.00	78.00	0.30	f	\N	t	2026-09-29 22:59:49	\N	0.650	\N
37	3	Camote	batata,boniato	86.00	1.60	20.10	0.10	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
38	3	Yuca	mandioca	160.00	1.40	38.10	0.30	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
39	3	Oca	\N	61.00	1.00	13.30	0.60	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
40	3	Racacha	arracacha,virraca	104.00	1.00	24.90	0.30	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
41	4	Carne de res	res,carne de vaca	250.00	26.00	0.00	15.00	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
42	4	Lomo de res	lomo	201.00	22.00	0.00	12.00	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
43	4	Carne molida de res	carne picada	254.00	17.20	0.00	20.00	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
44	4	Charque	charqui,carne seca	410.00	58.00	1.00	19.00	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
45	4	Carne de cerdo	chancho	242.00	27.30	0.00	14.00	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
46	4	Costilla de cerdo	\N	277.00	20.00	0.00	21.00	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
47	4	Carne de cordero	\N	294.00	25.00	0.00	21.00	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
48	4	Carne de llama	\N	110.00	24.80	0.00	1.00	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
49	4	Hígado de res	\N	135.00	20.40	3.90	3.60	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
50	5	Pollo entero	\N	239.00	27.00	0.00	14.00	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
51	5	Pechuga de pollo	\N	165.00	31.00	0.00	3.60	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
52	5	Pierna de pollo	muslo de pollo	209.00	26.00	0.00	11.00	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
53	5	Menudencia de pollo	\N	130.00	18.00	1.00	5.00	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
54	6	Trucha	\N	148.00	20.80	0.00	6.60	t	\N	t	2026-09-29 22:59:49	\N	\N	\N
55	6	Pejerrey	\N	105.00	19.00	0.00	2.50	t	\N	t	2026-09-29 22:59:49	\N	\N	\N
56	6	Surubí	\N	97.00	19.50	0.00	1.80	t	\N	t	2026-09-29 22:59:49	\N	\N	\N
57	6	Atún en lata	\N	128.00	23.60	0.00	3.00	t	\N	t	2026-09-29 22:59:49	\N	\N	\N
58	6	Camarón	\N	99.00	24.00	0.20	0.30	t	\N	t	2026-09-29 22:59:49	\N	\N	\N
59	7	Leche entera	leche	61.00	3.20	4.80	3.30	t	\N	t	2026-09-29 22:59:49	\N	1.030	\N
60	7	Leche evaporada	\N	134.00	6.80	10.00	7.60	t	\N	t	2026-09-29 22:59:49	\N	1.070	\N
61	7	Yogur natural	\N	59.00	3.50	4.70	3.30	t	\N	t	2026-09-29 22:59:49	\N	1.030	\N
62	7	Queso fresco	quesillo	264.00	17.00	3.00	20.00	t	\N	t	2026-09-29 22:59:49	\N	0.550	\N
63	7	Queso criollo	queso de Chiquitos	330.00	22.00	2.50	26.00	t	\N	t	2026-09-29 22:59:49	\N	\N	\N
64	7	Mantequilla	manteca	717.00	0.90	0.10	81.10	t	\N	t	2026-09-29 22:59:49	\N	0.950	\N
65	7	Crema de leche	nata	340.00	2.10	2.80	36.00	t	\N	t	2026-09-29 22:59:49	\N	1.000	\N
66	8	Huevo de gallina	huevo	143.00	12.60	0.70	9.50	t	\N	t	2026-09-29 22:59:49	\N	\N	\N
67	8	Huevo de codorniz	\N	158.00	13.10	0.40	11.10	t	\N	t	2026-09-29 22:59:49	\N	\N	\N
68	9	Arroz blanco	arroz	365.00	7.10	80.00	0.70	f	\N	t	2026-09-29 22:59:49	\N	0.850	\N
69	9	Quinua	quinoa	368.00	14.10	64.20	6.10	f	\N	t	2026-09-29 22:59:49	\N	0.750	\N
70	9	Cañahua	cañihua	344.00	14.00	63.40	4.30	f	\N	t	2026-09-29 22:59:49	\N	0.700	\N
72	9	Avena	\N	389.00	16.90	66.30	6.90	t	\N	t	2026-09-29 22:59:49	\N	0.400	\N
73	9	Maíz pelado	mote de maíz	365.00	9.40	74.30	4.70	f	\N	t	2026-09-29 22:59:49	\N	0.800	\N
74	9	Trigo pelado	\N	339.00	13.70	71.00	2.50	t	\N	t	2026-09-29 22:59:49	\N	0.800	\N
75	9	Fideo	pasta,tallarín	371.00	13.00	74.70	1.50	t	\N	t	2026-09-29 22:59:49	\N	\N	\N
76	10	Haba fresca	\N	88.00	7.90	14.00	0.70	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
77	10	Haba seca	\N	341.00	26.10	58.30	1.50	f	\N	t	2026-09-29 22:59:49	\N	0.780	\N
78	10	Arveja fresca	guisante	81.00	5.40	14.50	0.40	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
79	10	Frejol	poroto,frijol	333.00	23.60	60.00	0.80	f	\N	t	2026-09-29 22:59:49	\N	0.800	\N
71	9	Amaranto	kiwicha	371.00	13.60	65.20	7.00	f	ingredientes/a716ad31f97a0e9e4c8fa818fbf83b3f.jpg	t	2026-09-29 22:59:49	\N	0.750	\N
80	10	Lenteja	\N	353.00	25.80	60.10	1.10	f	\N	t	2026-09-29 22:59:49	\N	0.850	\N
81	10	Garbanzo	\N	364.00	19.30	60.70	6.00	f	\N	t	2026-09-29 22:59:49	\N	0.800	\N
82	10	Tarwi	chocho,lupino	277.00	17.30	17.60	17.50	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
83	11	Maní	cacahuate	567.00	25.80	16.10	49.20	t	\N	t	2026-09-29 22:59:49	\N	0.600	\N
84	11	Nuez	\N	654.00	15.20	13.70	65.20	t	\N	t	2026-09-29 22:59:49	\N	\N	\N
86	11	Semilla de girasol	pipa	584.00	20.80	20.00	51.50	t	\N	t	2026-09-29 22:59:49	\N	0.550	\N
87	11	Sésamo	ajonjolí	573.00	17.70	23.40	49.70	t	\N	t	2026-09-29 22:59:49	\N	0.600	\N
90	12	Manteca de cerdo	\N	902.00	0.00	0.00	100.00	f	\N	t	2026-09-29 22:59:49	\N	0.920	\N
91	13	Sal	sal de mesa	0.00	0.00	0.00	0.00	f	\N	t	2026-09-29 22:59:49	\N	1.200	\N
92	13	Pimienta negra	pimienta	251.00	10.40	64.00	3.30	f	\N	t	2026-09-29 22:59:49	\N	0.450	\N
93	13	Comino	\N	375.00	17.80	44.20	22.30	f	\N	t	2026-09-29 22:59:49	\N	0.450	\N
97	13	Canela	\N	247.00	4.00	80.60	1.20	f	\N	t	2026-09-29 22:59:49	\N	0.450	\N
98	13	Clavo de olor	\N	274.00	6.00	65.50	13.00	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
100	13	Palillo	cúrcuma	312.00	9.70	67.10	3.20	f	\N	t	2026-09-29 22:59:49	\N	0.550	\N
101	14	Perejil	\N	36.00	3.00	6.30	0.80	f	\N	t	2026-09-29 22:59:49	\N	0.250	\N
102	14	Huacataya	huacatay	44.00	3.50	7.00	1.00	f	\N	t	2026-09-29 22:59:49	\N	0.250	\N
103	14	Quirquiña	killkiña	40.00	3.00	6.50	0.90	f	\N	t	2026-09-29 22:59:49	\N	0.250	\N
104	14	Orégano	\N	265.00	9.00	68.90	4.30	f	\N	t	2026-09-29 22:59:49	\N	0.150	\N
105	14	Laurel	\N	313.00	7.60	75.00	8.40	f	\N	t	2026-09-29 22:59:49	\N	0.100	\N
106	15	Azúcar	azúcar blanca	387.00	0.00	100.00	0.00	f	\N	t	2026-09-29 22:59:49	\N	0.850	\N
107	15	Chancaca	panela,empanizao	380.00	0.50	98.00	0.10	f	\N	t	2026-09-29 22:59:49	\N	0.900	\N
108	15	Miel de abeja	miel	304.00	0.30	82.40	0.00	f	\N	t	2026-09-29 22:59:49	\N	1.400	\N
109	16	Harina de trigo	harina	364.00	10.30	76.30	1.00	t	\N	t	2026-09-29 22:59:49	\N	0.550	\N
110	16	Harina de maíz	\N	361.00	6.90	76.80	3.90	f	\N	t	2026-09-29 22:59:49	\N	0.600	\N
111	16	Pan molido	pan rallado,apanadura	395.00	13.40	71.80	5.30	t	\N	t	2026-09-29 22:59:49	\N	0.400	\N
112	16	Marraqueta	pan	277.00	9.00	53.00	3.00	t	\N	t	2026-09-29 22:59:49	\N	\N	\N
113	16	Levadura	\N	325.00	40.40	41.20	7.60	f	\N	t	2026-09-29 22:59:49	\N	0.500	\N
115	17	Vinagre	\N	21.00	0.00	0.90	0.00	f	\N	t	2026-09-29 22:59:49	\N	1.010	\N
116	17	Mayonesa	\N	680.00	1.00	0.60	75.00	t	\N	t	2026-09-29 22:59:49	\N	0.950	\N
117	17	Mostaza	\N	66.00	4.40	5.80	3.30	f	\N	t	2026-09-29 22:59:49	\N	1.050	\N
118	18	Arveja en lata	\N	60.00	4.00	10.00	0.40	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
119	18	Durazno en almíbar	\N	74.00	0.40	19.90	0.10	f	\N	t	2026-09-29 22:59:49	\N	\N	\N
120	18	Leche condensada	\N	321.00	7.90	54.40	8.70	t	\N	t	2026-09-29 22:59:49	\N	1.280	\N
122	20	Café	café molido	1.00	0.10	0.00	0.00	f	\N	t	2026-09-29 22:59:49	\N	0.400	\N
123	20	Vino tinto	\N	85.00	0.10	2.60	0.00	f	\N	t	2026-09-29 22:59:49	\N	0.990	\N
124	20	Cerveza	\N	43.00	0.50	3.60	0.00	f	\N	t	2026-09-29 22:59:49	\N	1.010	\N
89	12	Aceite de oliva	\N	884.00	0.00	0.00	100.00	f	ingredientes/5b3b54ef0778522122985c25fa70fffb.jpg	t	2026-09-29 22:59:49	\N	0.918	\N
88	12	Aceite vegetal	aceite de girasol,aceite	884.00	0.00	0.00	100.00	f	ingredientes/81aee0ce15d9c19bbd854e3417d5ff2a.webp	t	2026-09-29 22:59:49	\N	0.920	\N
10	1	Acelga	\N	19.00	1.80	3.70	0.20	f	ingredientes/b9df5e13fb8f842244cb9d3edf57308b.jpg	t	2026-09-29 22:59:49	\N	\N	\N
121	20	Agua	\N	0.00	0.00	0.00	0.00	f	ingredientes/e94ac1ec0e6b77b4e351a1dfdc8be74c.jpg	t	2026-09-29 22:59:49	\N	1.000	\N
6	1	Ají amarillo	ají	40.00	2.00	8.80	0.40	f	ingredientes/cd98e5db57e3a9fc58a20d61a6de4440.jpg	t	2026-09-29 22:59:49	\N	\N	\N
94	13	Ají amarillo molido	ají molido	282.00	10.90	55.00	8.00	f	ingredientes/a26f77026a4ed5581f41f206e849f72b.jpg	t	2026-09-29 22:59:49	\N	0.500	\N
95	13	Ají colorado molido	ají panca	282.00	10.90	55.00	8.00	f	ingredientes/e530f8d1c4c31446f9393c696deea750.jpg	t	2026-09-29 22:59:49	\N	0.500	\N
96	13	Ajo	\N	149.00	6.40	33.10	0.50	f	ingredientes/32ed58b7ce703059fa6a86ba9e037f77.jpg	t	2026-09-29 22:59:49	\N	0.600	\N
85	11	Almendra	\N	579.00	21.20	21.60	49.90	t	ingredientes/7dbdf59167990de330a9ac9e8f11e952.jpg	t	2026-09-29 22:59:49	\N	\N	\N
114	16	Almidón de maíz	maicena,chuño de maíz	381.00	0.30	91.30	0.10	f	ingredientes/f8948b8b2e91f578660386494f599520.webp	t	2026-09-29 22:59:49	\N	0.600	\N
99	13	Anís	\N	337.00	17.60	50.00	15.90	f	ingredientes/b3864a24b850a6354ac6b2b8a60ba8c6.webp	t	2026-09-29 22:59:49	\N	0.400	\N
17	1	Apio	\N	16.00	0.70	3.00	0.20	f	ingredientes/c54c8bb00b28ded2d396ebc2e4bc1ce5.jpg	t	2026-09-29 22:59:49	\N	\N	\N
128	5	Paloma	carne de paloma, pichón, palomino	122.90	20.60	0.00	4.50	f	\N	t	2026-10-09 11:58:54	\N	\N	\N
129	4	Cuy	cobayo, conejillo de indias, carne de cuy, cui	151.40	20.30	0.00	7.80	f	\N	t	2026-10-09 12:00:35	\N	\N	\N
\.


--
-- TOC entry 5291 (class 0 OID 105705)
-- Dependencies: 239
-- Data for Name: ingredientes_receta; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.ingredientes_receta (cod_ingrediente_receta, cod_receta_inr, cod_ingrediente_inr, cod_unidad_inr, can_inr, opc_inr, ord_inr, not_inr) FROM stdin;
1	1	41	2	600.000	f	1	cortada en filetes delgados
2	1	111	2	200.000	f	2	\N
3	1	66	13	6.000	f	3	2 para apanar, 4 para freír
4	1	68	11	2.000	f	4	\N
5	1	33	2	800.000	f	5	cortada en bastones
6	1	3	13	2.000	f	6	picado en cubos
7	1	1	13	1.000	f	7	picada fina
8	1	5	13	1.000	t	8	picado, para quien guste picante
9	1	88	7	250.000	f	9	para freír
10	1	91	9	2.000	f	10	\N
11	1	93	9	1.000	f	11	\N
12	2	42	2	700.000	f	1	en tiras
13	2	33	3	1.000	f	2	en bastones gruesos
14	2	1	13	2.000	f	3	en pluma
15	2	3	13	3.000	f	4	en gajos
16	2	5	13	2.000	f	5	en rodajas
17	2	66	13	4.000	f	6	duros, en cuartos
18	2	117	10	2.000	f	7	\N
19	2	88	7	200.000	f	8	\N
20	2	91	9	2.000	f	9	\N
21	2	92	9	1.000	f	10	\N
22	2	93	9	1.000	f	11	\N
23	3	83	2	250.000	f	1	pelado y tostado
24	3	41	2	500.000	f	2	en trozos con hueso
25	3	33	2	900.000	f	3	600 g en mitades para la sopa, 300 g en bastones para freír encima
26	3	78	2	150.000	f	4	\N
27	3	4	13	2.000	f	5	en cubos
28	3	1	13	1.000	f	6	picada
29	3	94	10	2.000	f	7	\N
30	3	68	11	0.500	f	8	\N
31	3	101	10	3.000	f	9	picado
32	3	91	10	1.000	f	10	\N
33	3	93	9	1.000	f	11	\N
34	4	44	2	400.000	f	1	remojado y desmenuzado
35	4	68	11	2.000	f	2	\N
36	4	1	13	1.000	f	3	picada fina
37	4	3	13	2.000	f	4	picado
38	4	7	13	1.000	f	5	picado
39	4	96	16	3.000	f	6	molido
40	4	23	13	2.000	f	7	maduros, en rodajas
41	4	66	13	4.000	f	8	\N
42	4	88	7	100.000	f	9	\N
43	4	100	9	1.000	f	10	o urucú
44	4	91	9	2.000	f	11	\N
45	4	93	9	1.000	f	12	\N
46	5	35	2	200.000	f	1	remojado y molido
47	5	47	2	500.000	f	2	en trozos
48	5	41	2	300.000	f	3	con hueso
49	5	33	2	500.000	f	4	en cubos
50	5	4	13	2.000	f	5	en cubos
51	5	76	2	200.000	t	6	peladas
52	5	74	2	100.000	f	7	remojado desde la víspera
53	5	1	13	1.000	f	8	picada
54	5	94	10	2.000	f	9	\N
55	5	102	10	2.000	f	10	picada
56	5	91	10	1.000	f	11	\N
57	5	93	9	1.000	f	12	\N
58	6	43	2	600.000	f	1	\N
59	6	1	13	2.000	f	2	picada fina
60	6	3	13	2.000	f	3	picado
61	6	78	2	200.000	f	4	\N
62	6	95	10	3.000	f	5	\N
63	6	33	2	500.000	f	6	en bastones para freír
64	6	68	11	1.500	f	7	\N
65	6	88	7	80.000	f	8	\N
66	6	91	9	2.000	f	9	\N
67	6	93	9	1.000	f	10	\N
68	6	104	9	1.000	f	11	\N
69	7	45	3	1.000	f	1	en trozos grandes
70	7	73	2	300.000	f	2	remojado desde la víspera
71	7	35	2	200.000	f	3	remojado
72	7	94	10	4.000	f	4	\N
73	7	1	13	2.000	f	5	picada
74	7	96	16	4.000	f	6	molido
75	7	111	10	3.000	f	7	para espesar
76	7	91	10	1.000	f	8	\N
77	7	93	9	1.000	f	9	\N
78	7	104	9	1.000	f	10	\N
79	8	69	11	1.000	f	1	lavada varias veces
80	8	4	13	2.000	f	2	en cubos
81	8	33	2	300.000	f	3	en cubos
82	8	1	13	1.000	f	4	picada
83	8	17	13	2.000	t	5	picado
84	8	101	10	2.000	f	6	picado
85	8	88	10	2.000	f	7	\N
86	8	91	9	2.000	f	8	\N
87	9	110	2	300.000	f	1	morada, remojada
88	9	106	2	250.000	f	2	\N
89	9	97	13	3.000	f	3	en rama
90	9	98	13	5.000	f	4	\N
91	9	21	13	1.000	t	5	la cáscara
92	9	109	2	400.000	f	6	para el pastel
93	9	62	2	150.000	f	7	para el relleno
94	9	88	7	400.000	f	8	para freír
95	9	91	9	1.000	f	9	\N
96	10	51	2	600.000	f	1	en cubos pequeños
97	10	109	3	1.000	f	2	\N
98	10	90	2	200.000	f	3	\N
99	10	33	2	400.000	f	4	en cubos pequeños
100	10	78	2	200.000	f	5	\N
101	10	2	13	6.000	f	6	picada
102	10	95	10	3.000	f	7	\N
103	10	66	13	3.000	f	8	duros, en rodajas
104	10	106	10	3.000	f	9	para la masa
105	10	100	9	1.000	f	10	\N
106	10	91	10	1.000	f	11	\N
107	10	93	9	1.000	f	12	\N
108	11	78	2	400.000	f	1	\N
109	11	33	2	400.000	f	2	en cubos
110	11	1	13	1.000	f	3	picada
111	11	4	13	1.000	f	4	rallada
112	11	62	2	100.000	t	5	en cubos, para servir
113	11	101	10	2.000	f	6	picado
114	11	88	10	2.000	f	7	\N
115	11	91	9	2.000	f	8	\N
116	12	68	11	2.000	f	1	\N
117	12	62	2	250.000	f	2	desmenuzado
118	12	59	11	1.000	f	3	\N
119	12	1	13	1.000	f	4	picada fina
120	12	64	10	2.000	f	5	\N
121	12	91	9	1.000	f	6	\N
122	13	69	11	1.000	f	1	lavada
123	13	3	13	2.000	f	2	en cubos
124	13	14	13	1.000	f	3	en cubos
125	13	1	13	0.500	f	4	en pluma fina
126	13	25	13	1.000	f	5	en cubos
127	13	21	13	2.000	f	6	el jugo
128	13	89	10	3.000	f	7	\N
129	13	101	10	3.000	f	8	picado
130	13	91	9	1.000	f	9	\N
131	14	75	2	200.000	f	1	\N
132	14	4	13	2.000	f	2	en cubos
133	14	33	2	300.000	f	3	en cubos
134	14	1	13	1.000	f	4	picada
135	14	17	13	1.000	t	5	picado
136	14	101	10	2.000	f	6	picado
137	14	88	10	2.000	f	7	\N
138	14	91	9	2.000	f	8	\N
139	15	50	3	1.800	f	1	trozado
140	15	33	3	1.000	f	2	en cuartos
141	15	1	13	2.000	f	3	en gajos
142	15	96	16	6.000	f	4	molido
143	15	21	13	2.000	f	5	el jugo
144	15	88	7	60.000	f	6	\N
145	15	104	10	1.000	f	7	\N
146	15	91	10	1.000	f	8	\N
147	15	92	9	1.000	f	9	\N
148	15	93	9	1.000	f	10	\N
149	16	50	3	1.500	f	1	en presas
150	16	95	10	4.000	f	2	\N
151	16	1	13	2.000	f	3	picada fina
152	16	96	16	4.000	f	4	molido
153	16	33	2	700.000	f	5	enteras pequeñas
154	16	35	2	200.000	f	6	remojado
155	16	78	2	150.000	t	7	\N
156	16	88	7	80.000	f	8	\N
157	16	91	10	1.000	f	9	\N
158	16	93	9	1.000	f	10	\N
159	16	101	10	3.000	f	11	picado
160	17	4	13	2.000	f	1	en cubos
161	17	33	2	300.000	f	2	en cubos
162	17	12	2	300.000	f	3	en cubos
163	17	16	2	150.000	f	4	en trozos
164	17	15	13	1.000	f	5	en rodajas
165	17	1	13	1.000	f	6	picada
166	17	17	13	2.000	f	7	picado
167	17	101	10	3.000	f	8	picado
168	17	88	10	2.000	f	9	\N
169	17	91	9	2.000	f	10	\N
170	18	68	11	3.000	f	1	cocido del día anterior
171	18	51	2	400.000	f	2	en cubos
172	18	66	13	3.000	f	3	batidos
173	18	2	13	6.000	f	4	picada
174	18	7	13	1.000	f	5	en tiras
175	18	96	16	3.000	f	6	molido
176	18	88	10	4.000	f	7	\N
177	18	91	9	2.000	f	8	\N
178	18	92	9	1.000	f	9	\N
179	19	33	2	800.000	f	1	enteras
180	19	62	2	250.000	f	2	\N
181	19	94	10	3.000	f	3	\N
182	19	60	7	200.000	f	4	\N
183	19	88	10	4.000	f	5	\N
184	19	66	13	2.000	f	6	duros, en rodajas
185	19	8	13	4.000	f	7	hojas enteras
186	19	91	9	1.000	f	8	\N
187	20	3	13	4.000	f	1	en gajos
188	20	1	13	1.000	f	2	en pluma fina
189	20	21	13	2.000	f	3	el jugo
190	20	5	13	1.000	t	4	picado
191	20	101	10	2.000	t	5	picado
192	20	88	10	2.000	f	6	\N
193	20	91	9	1.000	f	7	\N
194	21	31	13	6.000	f	1	la pulpa
195	21	121	8	1.500	f	2	fría
196	21	106	2	150.000	f	3	\N
197	22	29	2	300.000	f	1	deshidratado
198	22	106	2	250.000	f	2	\N
199	22	97	13	3.000	f	3	en rama
200	22	98	13	5.000	f	4	\N
201	22	121	8	3.000	f	5	\N
202	23	15	13	8.000	f	1	con su chala
203	23	62	2	300.000	f	2	en cubos
204	23	64	2	100.000	f	3	derretida
205	23	106	10	4.000	f	4	\N
206	23	97	9	1.000	f	5	molida
207	23	59	7	100.000	t	6	\N
208	23	91	9	1.000	f	7	\N
209	24	80	2	400.000	f	1	remojada
210	24	4	13	2.000	f	2	en cubos
211	24	33	2	400.000	f	3	en cubos
212	24	1	13	1.000	f	4	picada
213	24	3	13	2.000	f	5	picado
214	24	96	16	3.000	f	6	molido
215	24	88	10	3.000	f	7	\N
216	24	91	9	2.000	f	8	\N
217	24	93	9	1.000	f	9	\N
218	25	44	2	500.000	f	1	de llama, remojado
219	25	73	2	300.000	f	2	remojado desde la víspera
220	25	33	2	600.000	f	3	enteras
221	25	62	2	200.000	f	4	en rodajas
222	25	66	13	4.000	f	5	duros
223	25	5	13	2.000	t	6	para la llajua
224	25	3	13	2.000	t	7	para la llajua
225	25	88	7	100.000	f	8	\N
226	25	91	9	1.000	f	9	\N
227	26	5	13	3.000	f	1	sin venas si se quiere menos picante
228	26	3	13	3.000	f	2	\N
229	26	103	10	3.000	t	3	picada
230	26	102	10	2.000	t	4	picada
231	26	91	9	1.000	f	5	\N
232	27	10	2	400.000	f	1	en tiras
233	27	66	13	6.000	f	2	batidos
234	27	1	13	1.000	f	3	picada
235	27	62	2	100.000	t	4	desmenuzado
236	27	88	10	3.000	f	5	\N
237	27	91	9	1.000	f	6	\N
238	27	92	9	0.500	f	7	\N
239	28	68	11	1.000	f	1	\N
240	28	59	8	1.500	f	2	\N
241	28	106	2	200.000	f	3	\N
242	28	97	13	2.000	f	4	en rama
243	28	21	13	1.000	t	5	la cáscara
244	28	120	7	100.000	t	6	\N
245	29	109	2	500.000	f	1	\N
246	29	113	2	20.000	f	2	\N
247	29	106	2	50.000	f	3	\N
248	29	66	13	2.000	f	4	\N
249	29	88	7	500.000	f	5	para freír
250	29	107	2	250.000	f	6	para la miel
251	29	97	13	2.000	f	7	en rama
252	29	99	9	1.000	t	8	\N
253	29	91	9	1.000	f	9	\N
254	30	23	13	2.000	f	1	en rodajas
255	30	24	13	2.000	f	2	en cubos
256	30	27	2	300.000	f	3	en cubos
257	30	26	2	300.000	f	4	en cubos
258	30	22	13	3.000	f	5	el jugo
259	30	30	2	200.000	t	6	en mitades
260	30	108	10	2.000	t	7	\N
261	31	45	3	1.200	f	1	pierna con cuero
262	31	112	13	6.000	f	2	\N
263	31	1	13	3.000	f	3	en pluma, para el escabeche
264	31	4	13	2.000	f	4	rallada
265	31	115	7	100.000	f	5	\N
266	31	5	13	2.000	t	6	para la llajua
267	31	96	16	5.000	f	7	molido
268	31	91	10	1.000	f	8	\N
269	31	93	9	1.000	f	9	\N
270	31	104	9	1.000	f	10	\N
271	32	19	13	2.000	f	1	Por caducar
272	32	88	10	2.000	f	2	Para el ahogado
273	32	96	16	2.000	f	3	Picados finos
274	32	95	10	1.000	f	4	Ají colorado molido
275	32	93	9	0.500	f	5	Molido
276	32	91	17	1.000	f	6	Al gusto
277	32	121	7	200.000	f	7	Agua tibia
278	33	19	13	1.000	f	1	Por caducar
279	33	88	10	2.000	f	2	Para freír
280	33	94	9	1.000	f	3	Para dar sabor
281	33	91	17	1.000	f	4	Al gusto
282	34	19	13	2.000	f	1	Por caducar, usar ambas
283	34	95	10	1.000	f	2	Para dar color y sabor
284	34	96	16	2.000	f	3	Picados finos
285	34	88	10	3.000	f	4	Para humedecer
286	34	91	17	1.000	f	5	Al gusto
287	35	51	13	2.000	f	1	Pechuga de pollo en trozos
288	35	7	13	1.000	f	2	Pimiento morrón picado
289	35	95	10	2.000	f	3	Ají colorado molido
290	35	96	16	2.000	f	4	Ajo picado
291	35	88	10	2.000	f	5	Aceite vegetal
292	35	91	17	1.000	f	6	Sal
293	35	121	12	1.000	f	7	Agua
294	36	51	13	1.000	f	1	Pechuga de pollo entera
295	36	7	13	1.000	f	2	Pimiento morrón en tiras
296	36	93	9	1.000	f	3	Comino molido
297	36	101	10	1.000	f	4	Perejil picado
298	36	91	17	1.000	f	5	Sal
299	36	121	8	1.000	f	6	Agua para el caldo
\.


--
-- TOC entry 5296 (class 0 OID 105767)
-- Dependencies: 244
-- Data for Name: inventario; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.inventario (cod_inventario, cod_usuario_inv, cod_ingrediente_inv, cod_unidad_inv, can_inv, fec_cad_inv, not_inv, act_inv, fec_cre_inv, fec_act_inv) FROM stdin;
5	1	20	13	2.000	\N	\N	t	2026-10-06 22:43:38	\N
18	3	7	13	2.000	\N	\N	t	2026-10-07 15:08:43	\N
19	3	51	13	2.000	\N	\N	t	2026-10-07 15:08:59	\N
23	3	1	13	3.000	2026-10-06	PRUEBA aprovechamiento	t	2026-10-09 16:31:12	\N
24	3	5	2	250.000	2026-10-02	PRUEBA aprovechamiento	t	2026-10-09 16:31:12	\N
25	3	6	2	400.000	2026-10-09	PRUEBA aprovechamiento	t	2026-10-09 16:31:12	\N
\.


--
-- TOC entry 5318 (class 0 OID 105972)
-- Dependencies: 266
-- Data for Name: jobs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.jobs (id, queue, payload, attempts, reserved_at, available_at, created_at) FROM stdin;
\.


--
-- TOC entry 5310 (class 0 OID 105923)
-- Dependencies: 258
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.migrations (id, migration, batch) FROM stdin;
\.


--
-- TOC entry 5293 (class 0 OID 105733)
-- Dependencies: 241
-- Data for Name: pasos_receta; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.pasos_receta (cod_paso, cod_receta_pas, num_pas, ins_pas, tie_pas, img_pas, cri_pas) FROM stdin;
1	1	1	Cocer el arroz con agua y sal hasta que esté suelto. Reservar caliente.	20	\N	f
2	1	2	Golpear los filetes de carne hasta dejarlos muy delgados, casi transparentes.	10	\N	t
3	1	3	Sazonar la carne con sal y comino, pasarla por huevo batido y luego por pan molido.	8	\N	f
4	1	4	Freír las papas en aceite caliente hasta dorarlas. Escurrir sobre papel.	12	\N	f
5	1	5	Freír los filetes apanados hasta que estén dorados de ambos lados.	8	\N	t
6	1	6	Freír los huevos dejando la yema blanda.	4	\N	f
7	1	7	Picar el tomate, la cebolla y el locoto y mezclarlos con sal para la ensalada.	5	\N	f
8	1	8	Servir el arroz de base, encima la carne, las papas al costado, el huevo sobre la carne y la ensalada encima.	3	\N	f
9	2	1	Cocer los huevos en agua durante diez minutos y reservar.	10	\N	f
10	2	2	Freír las papas hasta que estén doradas y crocantes. Escurrir.	15	\N	f
11	2	3	Sazonar la carne con sal, pimienta y comino.	3	\N	f
12	2	4	Saltear la carne a fuego fuerte hasta sellarla por fuera.	8	\N	t
13	2	5	Agregar la cebolla y saltear dos minutos más, que quede firme.	3	\N	f
14	2	6	Sumar el tomate, el locoto y la mostaza. Cocinar un minuto.	2	\N	f
15	2	7	Montar las papas en la fuente, volcar encima el salteado y decorar con el huevo duro.	4	\N	f
16	3	1	Licuar el maní con dos tazas de agua hasta obtener una crema lisa sin grumos.	8	\N	t
17	3	2	Cocer la carne en tres litros de agua con sal durante media hora.	30	\N	f
18	3	3	Freír la cebolla con el ají amarillo y el comino hasta que suelte aroma.	6	\N	f
19	3	4	Incorporar el sofrito y el maní licuado al caldo, revolviendo para que no se corte.	4	\N	t
20	3	5	Agregar la zanahoria, la arveja y el arroz. Cocinar veinte minutos a fuego bajo.	20	\N	f
21	3	6	Sumar las papas y cocinar hasta que estén tiernas.	15	\N	f
22	3	7	Freír aparte las papas en bastones.	10	\N	f
23	3	8	Servir la sopa con perejil picado y las papas fritas encima.	2	\N	f
24	4	1	Remojar el charque en agua media hora, escurrirlo y desmenuzarlo con las manos.	30	\N	t
25	4	2	Freír el charque en aceite hasta que dore y quede suelto.	8	\N	f
26	4	3	Agregar cebolla, tomate, pimiento y ajo. Sofreír hasta que la cebolla esté transparente.	6	\N	f
27	4	4	Sumar el palillo, el comino y el arroz. Remover para que el grano tome color.	3	\N	t
28	4	5	Añadir cuatro tazas de agua caliente y sal. Cocer tapado a fuego bajo.	20	\N	f
29	4	6	Freír aparte los plátanos y los huevos.	8	\N	f
30	4	7	Servir el majadito con el plátano al costado y el huevo encima.	2	\N	f
31	5	1	Remojar el trigo y el chuño desde la noche anterior.	\N	\N	t
32	5	2	Cocer las carnes en tres litros de agua con sal durante cuarenta minutos.	40	\N	f
33	5	3	Agregar el trigo remojado y cocinar media hora más.	30	\N	f
34	5	4	Freír la cebolla con el ají amarillo y el comino hasta formar un ahogado.	6	\N	f
35	5	5	Incorporar el ahogado, la zanahoria y las habas al caldo.	3	\N	f
36	5	6	Moler el chuño remojado y agregarlo de a poco, revolviendo para que no se apelmace.	5	\N	t
37	5	7	Sumar las papas y cocinar hasta que todo esté tierno.	20	\N	f
38	5	8	Terminar con la huacataya picada justo antes de servir.	2	\N	f
39	6	1	Cocer el arroz con agua y sal.	20	\N	f
40	6	2	Freír la cebolla en aceite hasta que esté transparente.	5	\N	f
41	6	3	Agregar el ají colorado, el comino y el orégano. Freír dos minutos sin quemar.	3	\N	t
42	6	4	Incorporar la carne molida y deshacer los grumos con la cuchara.	6	\N	f
43	6	5	Sumar el tomate y un vaso de agua. Cocinar a fuego bajo.	15	\N	f
44	6	6	Añadir la arveja y cocinar hasta que esté tierna.	8	\N	f
45	6	7	Freír las papas aparte y servir el saice con arroz y papas fritas.	12	\N	f
46	7	1	Remojar el maíz y el chuño desde la noche anterior.	\N	\N	t
47	7	2	Cocer el maíz en abundante agua hasta que reviente.	60	\N	f
48	7	3	Freír la cebolla y el ajo, agregar el ají amarillo y el comino.	8	\N	t
49	7	4	Sellar el cerdo en el sofrito hasta dorarlo por fuera.	10	\N	f
50	7	5	Cubrir con tres litros de agua y cocinar a fuego medio.	60	\N	f
51	7	6	Agregar el chuño remojado y el orégano.	15	\N	f
52	7	7	Espesar con el pan molido disuelto en un poco de caldo.	5	\N	f
53	7	8	Servir el caldo con la carne, el mote y el chuño.	3	\N	f
54	8	1	Lavar la quinua frotándola bajo el agua hasta que no salga espuma.	5	\N	t
55	8	2	Sofreír la cebolla en aceite hasta que esté transparente.	4	\N	f
56	8	3	Agregar dos litros de agua, la quinua y la sal. Llevar a hervor.	8	\N	f
57	8	4	Sumar la zanahoria, la papa y el apio.	2	\N	f
58	8	5	Cocinar a fuego medio hasta que la quinua abra su germen y las verduras estén tiernas.	18	\N	f
59	8	6	Servir con perejil picado por encima.	1	\N	f
60	9	1	Remojar la harina de maíz en agua durante dos horas.	\N	\N	t
61	9	2	Hervir dos litros de agua con la canela, el clavo y la cáscara de limón.	10	\N	f
62	9	3	Colar el maíz remojado e incorporarlo al agua aromática removiendo sin parar.	5	\N	t
63	9	4	Cocinar a fuego bajo hasta que espese, unos treinta minutos, revolviendo seguido.	30	\N	t
64	9	5	Endulzar con el azúcar y retirar la canela y el clavo.	3	\N	f
65	9	6	Amasar la harina con agua tibia y sal hasta obtener una masa suave.	12	\N	f
66	9	7	Estirar la masa, colocar el queso, cerrar y freír en aceite caliente.	12	\N	f
67	9	8	Servir el api bien caliente con el pastel espolvoreado de azúcar.	2	\N	f
68	10	1	Cocer el pollo y reservar el caldo: ese caldo es el jugo de la salteña.	20	\N	t
69	10	2	Sofreír la cebolla verde con el ají colorado, el palillo y el comino.	8	\N	f
70	10	3	Agregar el pollo picado, la papa, la arveja y dos tazas del caldo.	5	\N	f
71	10	4	Cocinar hasta que espese y dejar enfriar por completo en la heladera.	\N	\N	t
72	10	5	Amasar la harina con la manteca derretida, el azúcar, la sal y agua tibia.	15	\N	f
73	10	6	Estirar la masa y cortar discos de unos quince centímetros.	15	\N	f
74	10	7	Rellenar cada disco con el jugo frío y una rodaja de huevo.	20	\N	t
75	10	8	Repulgar el borde cerrando bien para que no se escape el jugo.	15	\N	t
76	10	9	Hornear a horno muy fuerte hasta que doren.	20	\N	f
77	11	1	Sofreír la cebolla y la zanahoria en aceite.	5	\N	f
78	11	2	Agregar dos litros de agua y la arveja. Llevar a hervor.	8	\N	f
79	11	3	Cocinar veinte minutos hasta que la arveja esté blanda.	20	\N	f
80	11	4	Sumar la papa y cocinar hasta que esté tierna.	12	\N	f
81	11	5	Servir con queso fresco en cubos y perejil.	2	\N	f
82	12	1	Sofreír la cebolla en mantequilla hasta que esté transparente.	4	\N	f
83	12	2	Agregar el arroz y remover para que se impregne.	2	\N	f
84	12	3	Añadir cuatro tazas de agua caliente y sal. Cocer tapado.	18	\N	f
85	12	4	Incorporar la leche y el queso, removiendo hasta que se derrita.	4	\N	t
86	12	5	Dejar reposar tapado cinco minutos antes de servir.	5	\N	f
87	13	1	Lavar la quinua y cocerla en dos tazas de agua hasta que abra el germen.	18	\N	f
88	13	2	Escurrir y dejar enfriar por completo.	\N	\N	t
89	13	3	Picar todas las verduras en cubos parejos.	10	\N	f
90	13	4	Mezclar la quinua fría con las verduras.	3	\N	f
91	13	5	Aliñar con el jugo de limón, el aceite, la sal y el perejil.	2	\N	f
92	13	6	Agregar la palta al final para que no se deshaga.	1	\N	f
93	14	1	Sofreír la cebolla en aceite.	4	\N	f
94	14	2	Agregar dos litros de agua con sal y llevar a hervor.	8	\N	f
95	14	3	Sumar la zanahoria, la papa y el apio. Cocinar quince minutos.	15	\N	f
96	14	4	Añadir el fideo y cocinar hasta que esté a punto.	8	\N	t
97	14	5	Servir con perejil picado.	1	\N	f
98	15	1	Mezclar el ajo, el limón, el aceite, el orégano, la sal, la pimienta y el comino.	5	\N	f
99	15	2	Untar el pollo con esa mezcla y dejarlo reposar al menos treinta minutos.	\N	\N	t
100	15	3	Acomodar el pollo en una fuente con las papas y la cebolla alrededor.	5	\N	f
101	15	4	Hornear a fuego medio durante cuarenta minutos.	40	\N	f
102	15	5	Dar vuelta las papas y subir el fuego para dorar todo.	25	\N	f
103	15	6	Dejar reposar cinco minutos antes de trozar.	5	\N	f
104	16	1	Remojar el chuño desde la noche anterior y cocerlo aparte.	25	\N	t
105	16	2	Sofreír la cebolla y el ajo hasta que estén transparentes.	6	\N	f
106	16	3	Agregar el ají colorado y el comino. Freír dos minutos sin quemar.	3	\N	t
107	16	4	Sellar las presas de pollo en el ahogado.	8	\N	f
108	16	5	Cubrir con caldo o agua y cocinar a fuego medio.	30	\N	f
109	16	6	Cocer las papas aparte hasta que estén tiernas.	20	\N	f
110	16	7	Servir el pollo con su salsa, la papa y el chuño. Terminar con perejil.	3	\N	f
111	17	1	Sofreír la cebolla y el apio en aceite.	5	\N	f
112	17	2	Agregar dos litros de agua con sal.	3	\N	f
113	17	3	Sumar el choclo y la zanahoria, que tardan más en cocerse.	12	\N	f
114	17	4	Añadir el zapallo, la vainita y la papa.	15	\N	f
115	17	5	Servir con perejil picado por encima.	1	\N	f
116	18	1	Usar arroz frío del día anterior: el recién hecho se apelmaza.	\N	\N	t
117	18	2	Hacer una tortilla delgada con los huevos batidos y cortarla en tiras.	5	\N	f
118	18	3	Saltear el pollo a fuego fuerte con el ajo hasta dorarlo.	7	\N	f
119	18	4	Agregar el pimiento y la parte blanca de la cebolla verde.	3	\N	f
120	18	5	Sumar el arroz y saltear removiendo, sin aplastarlo.	5	\N	t
121	18	6	Incorporar la tortilla en tiras y la parte verde de la cebolla.	2	\N	f
122	18	7	Sazonar con sal y pimienta y servir enseguida.	1	\N	f
123	19	1	Cocer las papas con cáscara en agua con sal hasta que estén tiernas.	20	\N	f
124	19	2	Cocer los huevos diez minutos y reservar.	10	\N	f
125	19	3	Licuar el queso, el ají amarillo, la leche, el aceite y la sal hasta obtener una crema lisa.	5	\N	t
126	19	4	Pelar las papas y cortarlas en rodajas gruesas.	5	\N	f
127	19	5	Acomodar las hojas de lechuga en el plato y sobre ellas las papas.	3	\N	f
128	19	6	Bañar con la crema y decorar con el huevo duro.	2	\N	f
129	20	1	Cortar la cebolla en pluma fina y dejarla cinco minutos en agua fría con sal para que pierda el filo.	5	\N	f
130	20	2	Cortar el tomate en gajos.	4	\N	f
131	20	3	Escurrir la cebolla y mezclarla con el tomate.	1	\N	f
132	20	4	Aliñar con limón, aceite y sal justo antes de servir.	2	\N	t
133	21	1	Abrir los maracuyás y sacar toda la pulpa con una cuchara.	5	\N	f
134	21	2	Licuar la pulpa con medio litro de agua unos segundos, sin romper las pepas.	1	\N	t
135	21	3	Colar para separar las semillas.	3	\N	f
136	21	4	Mezclar con el resto del agua y endulzar al gusto.	2	\N	f
137	21	5	Servir bien frío.	1	\N	f
138	22	1	Remojar los duraznos deshidratados durante dos horas.	\N	\N	t
139	22	2	Hervir el agua con la canela y el clavo.	10	\N	f
140	22	3	Agregar los duraznos escurridos y cocinar a fuego bajo.	40	\N	f
141	22	4	Endulzar con el azúcar y dejar enfriar.	5	\N	f
142	22	5	Servir frío con un durazno entero en cada vaso.	2	\N	f
143	23	1	Deshojar los choclos con cuidado y reservar las chalas enteras.	15	\N	t
144	23	2	Desgranar los choclos y molerlos hasta obtener una pasta.	15	\N	f
145	23	3	Mezclar la pasta con la mantequilla, el azúcar, la sal y la canela.	5	\N	f
146	23	4	Si la masa está muy espesa, aflojar con un poco de leche.	2	\N	f
147	23	5	Poner una porción de masa sobre dos chalas cruzadas, colocar el queso y cerrar.	20	\N	t
148	23	6	Atar cada huminta con una tira de chala.	10	\N	f
149	23	7	Hornear a fuego medio hasta que la masa cuaje.	50	\N	f
150	24	1	Remojar la lenteja al menos dos horas.	\N	\N	t
151	24	2	Sofreír la cebolla, el ajo y el tomate con el comino.	6	\N	f
152	24	3	Agregar dos litros y medio de agua y la lenteja escurrida.	3	\N	f
153	24	4	Cocinar a fuego medio treinta minutos.	30	\N	f
154	24	5	Sumar la zanahoria y la papa y cocinar hasta que estén tiernas.	15	\N	f
155	25	1	Remojar el charque en agua durante una hora, cambiando el agua dos veces.	\N	\N	t
156	25	2	Cocer el maíz pelado hasta que reviente.	40	\N	f
157	25	3	Golpear el charque para ablandarlo y desmenuzarlo con las manos.	15	\N	t
158	25	4	Freír el charque desmenuzado hasta que quede crocante.	10	\N	f
159	25	5	Cocer las papas con cáscara y los huevos.	20	\N	f
160	25	6	Moler el tomate con el locoto para hacer la llajua.	5	\N	f
161	25	7	Servir el charque con el mote, la papa, el queso y el huevo. La llajua aparte.	3	\N	f
162	26	1	Lavar los locotos y quitarles las venas si se busca un picor suave.	4	\N	f
163	26	2	Moler los locotos con sal en batán o licuadora, sin hacerlos puré.	3	\N	t
164	26	3	Agregar el tomate y moler apenas, la llajua debe quedar con textura.	3	\N	f
165	26	4	Incorporar la quirquiña y la huacataya picadas.	2	\N	f
166	27	1	Lavar la acelga y cortarla en tiras finas.	5	\N	f
167	27	2	Sofreír la cebolla y agregar la acelga hasta que reduzca.	6	\N	f
168	27	3	Batir los huevos con sal y pimienta.	2	\N	f
169	27	4	Mezclar la acelga escurrida con el huevo y el queso.	2	\N	t
170	27	5	Cocinar a fuego bajo en sartén tapada hasta que cuaje.	8	\N	f
171	27	6	Dar vuelta con ayuda de un plato y dorar del otro lado.	3	\N	f
172	28	1	Hervir el arroz en dos tazas de agua cinco minutos y escurrir.	8	\N	f
173	28	2	Llevar la leche a hervor con la canela y la cáscara de limón.	8	\N	f
174	28	3	Agregar el arroz y cocinar a fuego muy bajo, revolviendo seguido.	30	\N	t
175	28	4	Endulzar con el azúcar cuando el arroz ya esté blando.	3	\N	t
176	28	5	Retirar la canela y la cáscara. Servir tibio o frío.	2	\N	f
177	29	1	Disolver la levadura en agua tibia con una cucharada de azúcar y dejar espumar.	10	\N	t
178	29	2	Mezclar la harina con la sal, el azúcar, los huevos y la levadura activada.	10	\N	f
179	29	3	Amasar hasta obtener una masa blanda y pegajosa.	12	\N	f
180	29	4	Tapar y dejar leudar hasta que doble su volumen.	\N	\N	t
181	29	5	Hervir la chancaca con agua, canela y anís hasta formar una miel.	15	\N	f
182	29	6	Estirar porciones de masa con las manos aceitadas y freír en aceite caliente.	15	\N	t
183	29	7	Escurrir sobre papel y bañar con la miel de chancaca.	5	\N	f
184	30	1	Exprimir las naranjas y reservar el jugo.	5	\N	f
185	30	2	Picar todas las frutas en cubos de tamaño parejo.	10	\N	f
186	30	3	Mezclar las frutas con el jugo de naranja, que evita que se oxiden.	2	\N	t
187	30	4	Endulzar con miel si hace falta y servir frío.	2	\N	f
188	31	1	Untar la pierna con ajo, sal, comino y orégano. Dejar reposar una hora.	\N	\N	t
189	31	2	Hornear a fuego medio cubierta con papel durante una hora.	60	\N	f
190	31	3	Destapar y subir el fuego para que el cuero quede crocante.	30	\N	t
191	31	4	Mezclar la cebolla y la zanahoria con vinagre y sal para el escabeche.	10	\N	f
192	31	5	Dejar reposar la carne diez minutos y cortarla en láminas.	10	\N	f
193	31	6	Abrir las marraquetas, rellenar con la carne, el escabeche y la llajua.	8	\N	f
194	32	1	Lavar bien el coliflor y separarlo en arbolitos medianos.	5	\N	f
195	32	2	Poner una olla al fuego medio y calentar el aceite vegetal.	3	\N	f
196	32	3	Freír el ajo picado hasta que comience a dorar un poco sin quemar.	2	\N	t
197	32	4	Añadir el ají colorado molido y el comino, removiendo constantemente para que cocine bien el ahogado.	4	\N	t
198	32	5	Incorporar los arbolitos de coliflor a la olla y mezclar bien con el ahogado.	3	\N	f
199	32	6	Agregar la sal y el agua, luego tapar la olla para que cocine a vapor y agarre sabor.	12	\N	f
200	32	7	Verificar que el coliflor esté bien cocido y apagar el fuego para servir caliente.	2	\N	f
201	33	1	Lava bien la pieza de coliflor y córtala en arbolitos pequeños.	5	\N	f
202	33	2	Calienta el aceite vegetal en una sartén grande a fuego medio.	3	\N	t
203	33	3	Echa los arbolitos de coliflor a la sartén junto con el ají amarillo molido y la sal.	2	\N	f
204	33	4	Mueve constantemente con una paleta para que se dore de manera uniforme.	10	\N	t
205	33	5	Retira del fuego cuando el coliflor esté bien tostado y crocante.	\N	\N	f
206	34	1	Precalienta el horno a temperatura moderada.	10	\N	t
207	34	2	Lava las dos piezas de coliflor y sepáralas en trozos grandes sobre una bandeja.	5	\N	f
208	34	3	Mezcla en un tazón pequeño el aceite vegetal, el ají colorado molido, los dientes de ajo y la sal.	5	\N	f
209	34	4	Unta la mezcla anterior por todo el coliflor asegurándote de cubrirlo bien.	5	\N	t
210	34	5	Introduce la bandeja al horno y hornea hasta que se dore la superficie.	30	\N	t
211	35	1	Corta la pechuga de pollo en cubos medianos.	5	\N	f
212	35	2	Pica finamente el ajo y corta el pimiento morrón en cuadraditos.	5	\N	f
213	35	3	Calienta el aceite en una olla y fríe el ajo junto con el ají colorado molido.	6	\N	t
214	35	4	Incorpora los trozos de pollo a la olla y sella la carne removiendo constantemente.	8	\N	t
215	35	5	Añade el pimiento morrón picado y sazona con sal al gusto.	3	\N	f
216	35	6	Vierte el vaso de agua, tapa la olla y deja cocer a fuego lento hasta que espese.	15	\N	t
217	36	1	Coloca el litro de agua en una olla y llévala a hervor.	5	\N	f
218	36	2	Introduce la pechuga de pollo entera dentro del agua hirviendo.	2	\N	t
219	36	3	Corta el pimiento morrón en tiras delgadas y agrégalo a la olla.	3	\N	f
220	36	4	Sazona el caldo con comino molido y sal al gusto.	2	\N	f
221	36	5	Deja cocinar a fuego medio hasta que el pollo esté tierno.	15	\N	t
222	36	6	Retira el pollo, desmenúzalo, devuélvelo a la sopa y espolvorea perejil antes de servir.	5	\N	f
\.


--
-- TOC entry 5312 (class 0 OID 105938)
-- Dependencies: 260
-- Data for Name: password_reset_tokens; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.password_reset_tokens (email, token, created_at) FROM stdin;
\.


--
-- TOC entry 5314 (class 0 OID 105946)
-- Dependencies: 262
-- Data for Name: personal_access_tokens; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.personal_access_tokens (id, tokenable_type, tokenable_id, name, token, abilities, last_used_at, expires_at, created_at, updated_at) FROM stdin;
\.


--
-- TOC entry 5274 (class 0 OID 105547)
-- Dependencies: 222
-- Data for Name: preferencias_dieta; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.preferencias_dieta (cod_preferencia, nom_pre, des_pre, act_pre, fec_cre_pre) FROM stdin;
1	Vegetariano	No consume carne ni pescado	t	2026-09-29 16:36:37
2	Vegano	No consume ningun producto de origen animal	t	2026-09-29 16:36:37
3	Sin gluten	Evita el trigo, la cebada y el centeno	t	2026-09-29 16:36:37
4	Sin lactosa	Evita la leche y sus derivados	t	2026-09-29 16:36:37
5	Bajo en sodio	Reduce el consumo de sal	t	2026-09-29 16:36:37
6	Bajo en carbohidratos	Reduce harinas, azucares y almidones	t	2026-09-29 16:36:37
7	Alto en proteinas	Prioriza preparaciones con mayor aporte proteico	t	2026-09-29 16:36:37
8	Sin frutos secos	Evita mani, nueces y almendras	t	2026-09-29 16:36:37
9	Sin azucar anadida	Evita el azucar agregado	t	2026-09-29 16:36:37
\.


--
-- TOC entry 5294 (class 0 OID 105750)
-- Dependencies: 242
-- Data for Name: receta_etiquetas; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.receta_etiquetas (cod_receta_ret, cod_etiqueta_ret, fec_cre_ret) FROM stdin;
1	12	2026-09-29 22:59:49
1	9	2026-09-29 22:59:49
2	12	2026-09-29 22:59:49
2	13	2026-09-29 22:59:49
2	14	2026-09-29 22:59:49
3	12	2026-09-29 22:59:49
3	16	2026-09-29 22:59:49
4	12	2026-09-29 22:59:49
4	3	2026-09-29 22:59:49
5	12	2026-09-29 22:59:49
5	16	2026-09-29 22:59:49
6	12	2026-09-29 22:59:49
6	4	2026-09-29 22:59:49
7	12	2026-09-29 22:59:49
7	13	2026-09-29 22:59:49
8	1	2026-09-29 22:59:49
8	4	2026-09-29 22:59:49
8	9	2026-09-29 22:59:49
9	12	2026-09-29 22:59:49
9	17	2026-09-29 22:59:49
10	12	2026-09-29 22:59:49
10	14	2026-09-29 22:59:49
11	3	2026-09-29 22:59:49
11	4	2026-09-29 22:59:49
11	1	2026-09-29 22:59:49
12	4	2026-09-29 22:59:49
12	3	2026-09-29 22:59:49
13	1	2026-09-29 22:59:49
13	5	2026-09-29 22:59:49
13	9	2026-09-29 22:59:49
13	10	2026-09-29 22:59:49
14	2	2026-09-29 22:59:49
14	3	2026-09-29 22:59:49
14	4	2026-09-29 22:59:49
15	4	2026-09-29 22:59:49
15	18	2026-09-29 22:59:49
16	12	2026-09-29 22:59:49
16	13	2026-09-29 22:59:49
17	5	2026-09-29 22:59:49
17	1	2026-09-29 22:59:49
17	10	2026-09-29 22:59:49
18	2	2026-09-29 22:59:49
18	18	2026-09-29 22:59:49
19	5	2026-09-29 22:59:49
19	4	2026-09-29 22:59:49
19	13	2026-09-29 22:59:49
20	6	2026-09-29 22:59:49
20	2	2026-09-29 22:59:49
20	3	2026-09-29 22:59:49
20	10	2026-09-29 22:59:49
21	2	2026-09-29 22:59:49
21	4	2026-09-29 22:59:49
21	1	2026-09-29 22:59:49
22	12	2026-09-29 22:59:49
22	3	2026-09-29 22:59:49
23	12	2026-09-29 22:59:49
23	5	2026-09-29 22:59:49
24	3	2026-09-29 22:59:49
24	1	2026-09-29 22:59:49
24	5	2026-09-29 22:59:49
24	9	2026-09-29 22:59:49
25	12	2026-09-29 22:59:49
25	9	2026-09-29 22:59:49
26	12	2026-09-29 22:59:49
26	13	2026-09-29 22:59:49
26	6	2026-09-29 22:59:49
26	2	2026-09-29 22:59:49
27	5	2026-09-29 22:59:49
27	2	2026-09-29 22:59:49
27	1	2026-09-29 22:59:49
28	17	2026-09-29 22:59:49
28	4	2026-09-29 22:59:49
28	18	2026-09-29 22:59:49
29	12	2026-09-29 22:59:49
29	17	2026-09-29 22:59:49
30	1	2026-09-29 22:59:49
30	6	2026-09-29 22:59:49
30	2	2026-09-29 22:59:49
30	10	2026-09-29 22:59:49
31	12	2026-09-29 22:59:49
31	14	2026-09-29 22:59:49
2	7	2026-09-29 23:44:11
3	7	2026-09-29 23:44:11
4	7	2026-09-29 23:44:11
6	7	2026-09-29 23:44:11
8	7	2026-09-29 23:44:11
11	7	2026-09-29 23:44:11
12	7	2026-09-29 23:44:11
13	7	2026-09-29 23:44:11
15	7	2026-09-29 23:44:11
16	7	2026-09-29 23:44:11
17	7	2026-09-29 23:44:11
18	7	2026-09-29 23:44:11
19	7	2026-09-29 23:44:11
20	7	2026-09-29 23:44:11
21	7	2026-09-29 23:44:11
22	7	2026-09-29 23:44:11
23	7	2026-09-29 23:44:11
24	7	2026-09-29 23:44:11
25	7	2026-09-29 23:44:11
26	7	2026-09-29 23:44:11
27	7	2026-09-29 23:44:11
28	7	2026-09-29 23:44:11
30	7	2026-09-29 23:44:11
1	8	2026-09-29 23:44:11
2	8	2026-09-29 23:44:11
3	8	2026-09-29 23:44:11
4	8	2026-09-29 23:44:11
5	8	2026-09-29 23:44:11
6	8	2026-09-29 23:44:11
7	8	2026-09-29 23:44:11
8	8	2026-09-29 23:44:11
10	8	2026-09-29 23:44:11
13	8	2026-09-29 23:44:11
14	8	2026-09-29 23:44:11
15	8	2026-09-29 23:44:11
16	8	2026-09-29 23:44:11
17	8	2026-09-29 23:44:11
18	8	2026-09-29 23:44:11
20	8	2026-09-29 23:44:11
21	8	2026-09-29 23:44:11
22	8	2026-09-29 23:44:11
24	8	2026-09-29 23:44:11
26	8	2026-09-29 23:44:11
29	8	2026-09-29 23:44:11
30	8	2026-09-29 23:44:11
31	8	2026-09-29 23:44:11
\.


--
-- TOC entry 5289 (class 0 OID 105667)
-- Dependencies: 237
-- Data for Name: recetas; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.recetas (cod_receta, cod_categoria_receta_rec, cod_usuario_rec, nom_rec, des_rec, tie_pre_rec, tie_coc_rec, por_rec, niv_rec, ori_rec, img_rec, cal_por_rec, pro_por_rec, car_por_rec, gra_por_rec, obs_nut_rec, est_val_rec, ver_rec, act_rec, fec_cre_rec, fec_act_rec, fec_eli_rec) FROM stdin;
1	8	\N	Silpancho cochabambino	Plato emblemático de Cochabamba: una milanesa delgada de carne sobre arroz y papas fritas, coronada con huevo frito y una ensalada cruda encima.	25	25	4	medio	sistema	\N	\N	\N	\N	\N	\N	pendiente	f	t	2026-09-29 22:59:49	\N	\N
3	4	\N	Sopa de maní	Sopa espesa y dorada, hecha con maní molido, carne de res y papas. Se sirve con papas fritas encima.	20	60	6	medio	sistema	\N	\N	\N	\N	\N	\N	pendiente	f	t	2026-09-29 22:59:49	\N	\N
4	8	\N	Majadito de charque	Arroz cocido junto con charque desmenuzado y urucú, típico del oriente. Se acompaña con plátano frito y huevo.	25	35	4	medio	sistema	\N	\N	\N	\N	\N	\N	pendiente	f	t	2026-09-29 22:59:49	\N	\N
5	4	\N	Chairo paceño	Sopa andina de chuño molido, carne y verduras. Contundente, pensada para el frío del altiplano.	30	90	6	dificil	sistema	\N	\N	\N	\N	\N	\N	pendiente	f	t	2026-09-29 22:59:49	\N	\N
6	8	\N	Saice tarijeño	Guiso de carne molida con ají colorado y arveja, típico de Tarija. Se sirve con arroz y fideo.	15	35	4	facil	sistema	\N	\N	\N	\N	\N	\N	pendiente	f	t	2026-09-29 22:59:49	\N	\N
7	4	\N	Fricasé paceño	Caldo picante de cerdo con mote de maíz y chuño, amarillo por el ají. Se come los domingos por la mañana.	25	120	6	dificil	sistema	\N	\N	\N	\N	\N	\N	pendiente	f	t	2026-09-29 22:59:49	\N	\N
8	4	\N	Sopa de quinua	Sopa ligera de quinua con verduras, sencilla de preparar y de alto valor nutritivo.	10	30	4	facil	sistema	\N	\N	\N	\N	\N	\N	pendiente	f	t	2026-09-29 22:59:49	\N	\N
9	1	\N	Api con pastel	Bebida caliente de maíz morado con canela y clavo, acompañada de un pastel frito espolvoreado con azúcar.	20	40	6	medio	sistema	\N	\N	\N	\N	\N	\N	pendiente	f	t	2026-09-29 22:59:49	\N	\N
10	11	\N	Salteñas de pollo	Empanada jugosa de horno rellena de pollo, papa y arveja en un jugo que cuaja al enfriarse.	60	25	12	dificil	sistema	\N	\N	\N	\N	\N	\N	pendiente	f	t	2026-09-29 22:59:49	\N	\N
11	4	\N	Sopa de arveja	Sopa casera de arveja verde con papa y un toque de queso, fácil y económica.	10	35	4	facil	sistema	\N	\N	\N	\N	\N	\N	pendiente	f	t	2026-09-29 22:59:49	\N	\N
12	7	\N	Arroz con queso	Arroz cremoso con queso fresco derretido, guarnición clásica del valle.	5	25	4	facil	sistema	\N	\N	\N	\N	\N	\N	pendiente	f	t	2026-09-29 22:59:49	\N	\N
13	5	\N	Ensalada de quinua	Ensalada fría de quinua con verduras crudas y limón. Liviana y con buena carga de proteína.	15	20	4	facil	sistema	\N	\N	\N	\N	\N	\N	pendiente	f	t	2026-09-29 22:59:49	\N	\N
14	4	\N	Sopa de fideo	Sopa rápida de fideo con verduras, la que se hace cuando hay poco tiempo.	10	25	4	facil	sistema	\N	\N	\N	\N	\N	\N	pendiente	f	t	2026-09-29 22:59:49	\N	\N
15	6	\N	Pollo al horno con papas	Pollo entero horneado con papas doradas alrededor. Se prepara solo una vez que entra al horno.	15	70	6	facil	sistema	\N	\N	\N	\N	\N	\N	pendiente	f	t	2026-09-29 22:59:49	\N	\N
16	6	\N	Picante de pollo	Pollo guisado en ají colorado, servido con papa y chuño. Plato de fiesta en el altiplano.	20	45	5	medio	sistema	\N	\N	\N	\N	\N	\N	pendiente	f	t	2026-09-29 22:59:49	\N	\N
17	4	\N	Sopa de verduras	Caldo de verduras de temporada, sin carne. Liviana y apta para vegetarianos.	15	30	4	facil	sistema	\N	\N	\N	\N	\N	\N	pendiente	f	t	2026-09-29 22:59:49	\N	\N
18	6	\N	Arroz chaufa boliviano	Arroz salteado con pollo, huevo y cebolla verde. Versión local del plato chino, muy común en almuerzos.	15	20	4	facil	sistema	\N	\N	\N	\N	\N	\N	pendiente	f	t	2026-09-29 22:59:49	\N	\N
19	11	\N	Papas a la huancaína	Papas cocidas bañadas en una crema de ají amarillo y queso. Se sirve frío, sobre lechuga.	15	20	4	facil	sistema	\N	\N	\N	\N	\N	\N	pendiente	f	t	2026-09-29 22:59:49	\N	\N
20	5	\N	Ensalada de tomate y cebolla	La ensalada que acompaña casi todo en Bolivia: tomate, cebolla, sal y limón.	10	0	4	facil	sistema	\N	\N	\N	\N	\N	\N	pendiente	f	t	2026-09-29 22:59:49	\N	\N
21	12	\N	Jugo de maracuyá	Refresco frío de maracuyá, ácido y dulce a la vez.	10	0	4	facil	sistema	\N	\N	\N	\N	\N	\N	pendiente	f	t	2026-09-29 22:59:49	\N	\N
22	12	\N	Mocochinchi	Refresco de durazno deshidratado cocido con canela y clavo, se toma helado.	10	45	8	facil	sistema	\N	\N	\N	\N	\N	\N	pendiente	f	t	2026-09-29 22:59:49	\N	\N
23	11	\N	Humintas al horno	Pasta de choclo con queso, envuelta en su propia chala y horneada.	40	50	8	medio	sistema	\N	\N	\N	\N	\N	\N	pendiente	f	t	2026-09-29 22:59:49	\N	\N
24	4	\N	Sopa de lenteja	Sopa espesa de lenteja con verduras, barata y muy nutritiva.	10	45	5	facil	sistema	\N	\N	\N	\N	\N	\N	pendiente	f	t	2026-09-29 22:59:49	\N	\N
25	8	\N	Charquekán orureño	Charque de llama desmenuzado y frito, servido con mote, papa y queso. Plato de Oruro.	30	40	4	medio	sistema	\N	\N	\N	\N	\N	\N	pendiente	f	t	2026-09-29 22:59:49	\N	\N
26	13	\N	Llajua	Salsa picante de locoto y tomate que acompaña casi cualquier plato boliviano.	10	0	8	facil	sistema	\N	\N	\N	\N	\N	\N	pendiente	f	t	2026-09-29 22:59:49	\N	\N
27	3	\N	Tortilla de acelga	Tortilla de huevo con acelga salteada y queso, rápida para la noche.	10	15	3	facil	sistema	\N	\N	\N	\N	\N	\N	pendiente	f	t	2026-09-29 22:59:49	\N	\N
28	9	\N	Arroz con leche	Postre de arroz cocido en leche con canela, servido tibio o frío.	5	40	6	facil	sistema	\N	\N	\N	\N	\N	\N	pendiente	f	t	2026-09-29 22:59:49	\N	\N
29	9	\N	Buñuelos	Masa frita esponjosa bañada en miel de chancaca, típica de fiestas y ferias.	90	20	8	medio	sistema	\N	\N	\N	\N	\N	\N	pendiente	f	t	2026-09-29 22:59:49	\N	\N
30	9	\N	Ensalada de frutas	Frutas de temporada picadas con jugo de naranja. Fresca y sin cocción.	15	0	4	facil	sistema	\N	\N	\N	\N	\N	\N	pendiente	f	t	2026-09-29 22:59:49	\N	\N
31	11	\N	Sándwich de chola	Marraqueta rellena de cerdo horneado con su cuero crocante, escabeche de cebolla y llajua.	20	90	6	medio	sistema	\N	\N	\N	\N	\N	\N	pendiente	f	t	2026-09-29 22:59:49	\N	\N
32	2	1	Coliflor ahogado con ají colorado	Un plato sencillo y bien casero de coliflor ahogado con su buen ají colorado, ideal para aprovechar lo que hay en casa.	10	20	2	facil	ia_generada	\N	\N	\N	\N	\N	\N	pendiente	f	t	2026-10-06 22:19:55	\N	\N
33	11	1	Coliflor frito crujiente con ají amarillo	Unos deliciosos bocaditos de coliflor fritos con un toque picante ideal para picar.	10	15	2	facil	ia_generada	\N	\N	\N	\N	\N	\N	pendiente	f	t	2026-10-06 22:40:23	\N	\N
34	7	1	Coliflor al horno con ají colorado y ajo	Un acompañamiento horneado y sabroso que aprovecha el coliflor maduro con ají y ajo.	15	30	3	medio	ia_generada	\N	\N	\N	\N	\N	\N	pendiente	f	t	2026-10-06 22:40:24	\N	\N
35	8	3	Ají de pollo con pimiento	Un guiso tradicional boliviano preparado con trozos de pollo, ají colorado y pimiento morrón.	15	30	2	medio	ia_generada	\N	\N	\N	\N	\N	\N	pendiente	f	t	2026-10-07 15:18:48	\N	\N
36	4	3	Sopa casera de pollo con morrón	Una reconfortante sopa casera elaborada con caldo de pollo, pimiento morrón y especias.	10	25	2	facil	ia_generada	\N	\N	\N	\N	\N	\N	pendiente	f	t	2026-10-07 15:19:01	\N	\N
2	8	\N	Pique macho	Fuente compartida de carne y salchicha salteadas sobre papas fritas, con cebolla, tomate y locoto. Se come al centro de la mesa.	20	30	4	medio	sistema	\N	\N	\N	\N	\N	\N	aprobada	t	t	2026-09-29 22:59:49	2026-10-08 20:39:10	\N
\.


--
-- TOC entry 5272 (class 0 OID 105536)
-- Dependencies: 220
-- Data for Name: roles; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.roles (cod_rol, nom_rol, des_rol, act_rol, fec_cre_rol) FROM stdin;
1	Administrador	Administra usuarios, catalogos, reportes y monitoreo del sistema.	t	2026-09-29 16:36:37
2	Nutricionista	Revisa, observa y aprueba las recetas y su informacion nutricional.	t	2026-09-29 16:36:37
3	Usuario	Gestiona su inventario y genera recetas con los ingredientes disponibles.	t	2026-09-29 16:36:37
\.


--
-- TOC entry 5322 (class 0 OID 113709)
-- Dependencies: 271
-- Data for Name: salidas_inventario; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.salidas_inventario (cod_salida, cod_usuario_sal, cod_ingrediente_sal, cod_unidad_sal, can_sal, mot_sal, cau_sal, cod_receta_sal, fec_cad_sal, not_sal, fec_sal) FROM stdin;
1	3	3	3	1.000	consumido	\N	\N	2026-10-08	\N	2026-10-09 16:32:05
2	3	4	2	500.000	desperdiciado	mal_estado	\N	2026-10-08	\N	2026-10-09 16:32:06
3	1	19	13	2.000	sin_resolver	\N	\N	2026-10-01	\N	2026-10-09 16:32:22
4	3	33	3	2.000	sin_resolver	\N	\N	2026-09-29	\N	2026-10-09 16:32:22
\.


--
-- TOC entry 5311 (class 0 OID 105929)
-- Dependencies: 259
-- Data for Name: sessions; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.sessions (id, user_id, ip_address, user_agent, payload, last_activity) FROM stdin;
\.


--
-- TOC entry 5281 (class 0 OID 105608)
-- Dependencies: 229
-- Data for Name: unidades_medida; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.unidades_medida (cod_unidad, nom_uni, abr_uni, tip_uni, fac_uni, act_uni) FROM stdin;
1	Miligramos	mg	masa	0.001000	t
2	Gramos	g	masa	1.000000	t
3	Kilogramos	kg	masa	1000.000000	t
4	Libras	lb	masa	453.592000	t
6	Arrobas	@	masa	11500.000000	t
7	Mililitros	ml	volumen	1.000000	t
8	Litros	l	volumen	1000.000000	t
9	Cucharadita	cdta	volumen	5.000000	t
10	Cucharada	cda	volumen	15.000000	t
11	Taza	tz	volumen	240.000000	t
12	Vaso	vs	volumen	200.000000	t
13	Pieza	pz	unidad	1.000000	t
14	Docena	doc	unidad	12.000000	t
15	Rebanada	reb	unidad	1.000000	t
16	Diente	dte	unidad	1.000000	t
17	Pizca	pzc	unidad	1.000000	t
18	Al gusto	ag	unidad	1.000000	t
5	Onzas	oz	masa	28.349527	t
\.


--
-- TOC entry 5277 (class 0 OID 105578)
-- Dependencies: 225
-- Data for Name: usuario_preferencias; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.usuario_preferencias (cod_usuario_upr, cod_preferencia_upr, fec_cre_upr) FROM stdin;
1	2	2026-09-30 08:44:22
\.


--
-- TOC entry 5276 (class 0 OID 105558)
-- Dependencies: 224
-- Data for Name: usuarios; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.usuarios (cod_usuario, cod_rol_usu, nom_usu, cor_usu, con_usu, fot_usu, act_usu, ult_acc_usu, remember_token, fec_cre_usu, fec_act_usu, fec_eli_usu) FROM stdin;
2	2	Nutricionista de Prueba	nutricionista@recetas.bo	$2y$12$bNHPxygz/Lpl8twqWStj3ukmrgTFPOjkk/C6jCoDlYf/dNpEFcNFK	\N	t	2026-10-07 02:54:57	\N	2026-09-29 16:36:37	\N	\N
4	3	Mariela	Mariela@gmail.com	$2y$12$m1rzWI8RH/BcmaqAG/FwZO9nGWQcYxwwRXnfNr.PRNbs1tWZu9e4C	\N	t	\N	\N	2026-10-08 14:43:24	\N	\N
5	3	Mariela	Marielas@gmail.com	$2y$12$02C6jDns8eh.UxnmjCGw6ueOmh0VPjRDGSKzeIwVXKPc.DzzWCt3S	\N	t	\N	\N	2026-10-08 14:46:50	\N	\N
1	1	Administrador del Sistema	admin@recetas.bo	$2y$12$58bJRoFUv520Cq.Cai67VOSqtph.iWJsOfBD4dk8beyxpDW34NSna	\N	t	2026-10-09 20:33:03	\N	2026-09-29 16:36:37	\N	\N
3	3	Usuario de Prueba	usuario@recetas.bo	$2y$12$qAkrvK3k50kNd3eMzOVkoux/oK7PprWPAaTLN/HMNK0Jp0.21thbi	\N	t	2026-10-09 20:34:13	\N	2026-09-29 16:36:37	\N	\N
\.


--
-- TOC entry 5306 (class 0 OID 105881)
-- Dependencies: 254
-- Data for Name: validaciones_receta; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.validaciones_receta (cod_validacion, cod_receta_vre, cod_usuario_vre, est_vre, obs_vre, val_ing_vre, val_pas_vre, val_nut_vre, fec_rev_vre) FROM stdin;
1	2	1	aprobada	\N	t	t	t	2026-10-08 16:39:10
\.


--
-- TOC entry 5302 (class 0 OID 105838)
-- Dependencies: 250
-- Data for Name: valoraciones; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.valoraciones (cod_valoracion, cod_usuario_val, cod_receta_val, pun_val, com_val, fec_cre_val, fec_act_val) FROM stdin;
\.


--
-- TOC entry 5386 (class 0 OID 0)
-- Dependencies: 255
-- Name: bitacora_cod_bitacora_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.bitacora_cod_bitacora_seq', 149, true);


--
-- TOC entry 5387 (class 0 OID 0)
-- Dependencies: 226
-- Name: categorias_ingrediente_cod_categoria_ingrediente_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.categorias_ingrediente_cod_categoria_ingrediente_seq', 21, true);


--
-- TOC entry 5388 (class 0 OID 0)
-- Dependencies: 232
-- Name: categorias_receta_cod_categoria_receta_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.categorias_receta_cod_categoria_receta_seq', 13, true);


--
-- TOC entry 5389 (class 0 OID 0)
-- Dependencies: 234
-- Name: etiquetas_cod_etiqueta_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.etiquetas_cod_etiqueta_seq', 18, true);


--
-- TOC entry 5390 (class 0 OID 0)
-- Dependencies: 267
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.failed_jobs_id_seq', 1, false);


--
-- TOC entry 5391 (class 0 OID 0)
-- Dependencies: 251
-- Name: favoritos_cod_favorito_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.favoritos_cod_favorito_seq', 1, false);


--
-- TOC entry 5392 (class 0 OID 0)
-- Dependencies: 245
-- Name: historial_consulta_cod_consulta_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.historial_consulta_cod_consulta_seq', 15, true);


--
-- TOC entry 5393 (class 0 OID 0)
-- Dependencies: 247
-- Name: historial_resultado_cod_resultado_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.historial_resultado_cod_resultado_seq', 1, true);


--
-- TOC entry 5394 (class 0 OID 0)
-- Dependencies: 230
-- Name: ingredientes_cod_ingrediente_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.ingredientes_cod_ingrediente_seq', 129, true);


--
-- TOC entry 5395 (class 0 OID 0)
-- Dependencies: 238
-- Name: ingredientes_receta_cod_ingrediente_receta_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.ingredientes_receta_cod_ingrediente_receta_seq', 299, true);


--
-- TOC entry 5396 (class 0 OID 0)
-- Dependencies: 243
-- Name: inventario_cod_inventario_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.inventario_cod_inventario_seq', 25, true);


--
-- TOC entry 5397 (class 0 OID 0)
-- Dependencies: 265
-- Name: jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.jobs_id_seq', 1, false);


--
-- TOC entry 5398 (class 0 OID 0)
-- Dependencies: 257
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.migrations_id_seq', 1, false);


--
-- TOC entry 5399 (class 0 OID 0)
-- Dependencies: 240
-- Name: pasos_receta_cod_paso_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.pasos_receta_cod_paso_seq', 222, true);


--
-- TOC entry 5400 (class 0 OID 0)
-- Dependencies: 261
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.personal_access_tokens_id_seq', 1, false);


--
-- TOC entry 5401 (class 0 OID 0)
-- Dependencies: 221
-- Name: preferencias_dieta_cod_preferencia_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.preferencias_dieta_cod_preferencia_seq', 10, true);


--
-- TOC entry 5402 (class 0 OID 0)
-- Dependencies: 236
-- Name: recetas_cod_receta_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.recetas_cod_receta_seq', 36, true);


--
-- TOC entry 5403 (class 0 OID 0)
-- Dependencies: 219
-- Name: roles_cod_rol_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.roles_cod_rol_seq', 3, true);


--
-- TOC entry 5404 (class 0 OID 0)
-- Dependencies: 270
-- Name: salidas_inventario_cod_salida_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.salidas_inventario_cod_salida_seq', 4, true);


--
-- TOC entry 5405 (class 0 OID 0)
-- Dependencies: 228
-- Name: unidades_medida_cod_unidad_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.unidades_medida_cod_unidad_seq', 18, true);


--
-- TOC entry 5406 (class 0 OID 0)
-- Dependencies: 223
-- Name: usuarios_cod_usuario_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.usuarios_cod_usuario_seq', 5, true);


--
-- TOC entry 5407 (class 0 OID 0)
-- Dependencies: 253
-- Name: validaciones_receta_cod_validacion_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.validaciones_receta_cod_validacion_seq', 1, true);


--
-- TOC entry 5408 (class 0 OID 0)
-- Dependencies: 249
-- Name: valoraciones_cod_valoracion_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.valoraciones_cod_valoracion_seq', 1, false);


--
-- TOC entry 5063 (class 2606 OID 105914)
-- Name: bitacora bitacora_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.bitacora
    ADD CONSTRAINT bitacora_pkey PRIMARY KEY (cod_bitacora);


--
-- TOC entry 5081 (class 2606 OID 105970)
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- TOC entry 5079 (class 2606 OID 105963)
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- TOC entry 5001 (class 2606 OID 105606)
-- Name: categorias_ingrediente categorias_ingrediente_nom_cat_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categorias_ingrediente
    ADD CONSTRAINT categorias_ingrediente_nom_cat_key UNIQUE (nom_cat);


--
-- TOC entry 5003 (class 2606 OID 105604)
-- Name: categorias_ingrediente categorias_ingrediente_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categorias_ingrediente
    ADD CONSTRAINT categorias_ingrediente_pkey PRIMARY KEY (cod_categoria_ingrediente);


--
-- TOC entry 5015 (class 2606 OID 105653)
-- Name: categorias_receta categorias_receta_nom_car_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categorias_receta
    ADD CONSTRAINT categorias_receta_nom_car_key UNIQUE (nom_car);


--
-- TOC entry 5017 (class 2606 OID 105651)
-- Name: categorias_receta categorias_receta_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categorias_receta
    ADD CONSTRAINT categorias_receta_pkey PRIMARY KEY (cod_categoria_receta);


--
-- TOC entry 5019 (class 2606 OID 105665)
-- Name: etiquetas etiquetas_nom_eti_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.etiquetas
    ADD CONSTRAINT etiquetas_nom_eti_key UNIQUE (nom_eti);


--
-- TOC entry 5021 (class 2606 OID 105663)
-- Name: etiquetas etiquetas_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.etiquetas
    ADD CONSTRAINT etiquetas_pkey PRIMARY KEY (cod_etiqueta);


--
-- TOC entry 5086 (class 2606 OID 105990)
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- TOC entry 5088 (class 2606 OID 105992)
-- Name: failed_jobs failed_jobs_uuid_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_key UNIQUE (uuid);


--
-- TOC entry 5055 (class 2606 OID 105867)
-- Name: favoritos favoritos_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.favoritos
    ADD CONSTRAINT favoritos_pkey PRIMARY KEY (cod_favorito);


--
-- TOC entry 5057 (class 2606 OID 105869)
-- Name: favoritos favoritos_usuario_receta_uk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.favoritos
    ADD CONSTRAINT favoritos_usuario_receta_uk UNIQUE (cod_usuario_fav, cod_receta_fav);


--
-- TOC entry 5045 (class 2606 OID 105806)
-- Name: historial_consulta historial_consulta_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.historial_consulta
    ADD CONSTRAINT historial_consulta_pkey PRIMARY KEY (cod_consulta);


--
-- TOC entry 5049 (class 2606 OID 105825)
-- Name: historial_resultado historial_resultado_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.historial_resultado
    ADD CONSTRAINT historial_resultado_pkey PRIMARY KEY (cod_resultado);


--
-- TOC entry 5011 (class 2606 OID 105634)
-- Name: ingredientes ingredientes_nombre_categoria_uk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ingredientes
    ADD CONSTRAINT ingredientes_nombre_categoria_uk UNIQUE (nom_ing, cod_categoria_ingrediente_ing);


--
-- TOC entry 5013 (class 2606 OID 105632)
-- Name: ingredientes ingredientes_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ingredientes
    ADD CONSTRAINT ingredientes_pkey PRIMARY KEY (cod_ingrediente);


--
-- TOC entry 5029 (class 2606 OID 105713)
-- Name: ingredientes_receta ingredientes_receta_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ingredientes_receta
    ADD CONSTRAINT ingredientes_receta_pkey PRIMARY KEY (cod_ingrediente_receta);


--
-- TOC entry 5031 (class 2606 OID 105715)
-- Name: ingredientes_receta ingredientes_receta_uk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ingredientes_receta
    ADD CONSTRAINT ingredientes_receta_uk UNIQUE (cod_receta_inr, cod_ingrediente_inr);


--
-- TOC entry 5040 (class 2606 OID 105776)
-- Name: inventario inventario_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inventario
    ADD CONSTRAINT inventario_pkey PRIMARY KEY (cod_inventario);


--
-- TOC entry 5043 (class 2606 OID 105778)
-- Name: inventario inventario_usuario_ingrediente_uk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inventario
    ADD CONSTRAINT inventario_usuario_ingrediente_uk UNIQUE (cod_usuario_inv, cod_ingrediente_inv);


--
-- TOC entry 5083 (class 2606 OID 105979)
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- TOC entry 5066 (class 2606 OID 105928)
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- TOC entry 5033 (class 2606 OID 105744)
-- Name: pasos_receta pasos_receta_orden_uk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pasos_receta
    ADD CONSTRAINT pasos_receta_orden_uk UNIQUE (cod_receta_pas, num_pas);


--
-- TOC entry 5035 (class 2606 OID 105742)
-- Name: pasos_receta pasos_receta_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pasos_receta
    ADD CONSTRAINT pasos_receta_pkey PRIMARY KEY (cod_paso);


--
-- TOC entry 5072 (class 2606 OID 105944)
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- TOC entry 5074 (class 2606 OID 105953)
-- Name: personal_access_tokens personal_access_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_pkey PRIMARY KEY (id);


--
-- TOC entry 5076 (class 2606 OID 105955)
-- Name: personal_access_tokens personal_access_tokens_token_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_token_key UNIQUE (token);


--
-- TOC entry 4990 (class 2606 OID 105556)
-- Name: preferencias_dieta preferencias_dieta_nom_pre_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.preferencias_dieta
    ADD CONSTRAINT preferencias_dieta_nom_pre_key UNIQUE (nom_pre);


--
-- TOC entry 4992 (class 2606 OID 105554)
-- Name: preferencias_dieta preferencias_dieta_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.preferencias_dieta
    ADD CONSTRAINT preferencias_dieta_pkey PRIMARY KEY (cod_preferencia);


--
-- TOC entry 5037 (class 2606 OID 105755)
-- Name: receta_etiquetas receta_etiquetas_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.receta_etiquetas
    ADD CONSTRAINT receta_etiquetas_pk PRIMARY KEY (cod_receta_ret, cod_etiqueta_ret);


--
-- TOC entry 5026 (class 2606 OID 105690)
-- Name: recetas recetas_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.recetas
    ADD CONSTRAINT recetas_pkey PRIMARY KEY (cod_receta);


--
-- TOC entry 4986 (class 2606 OID 105545)
-- Name: roles roles_nom_rol_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_nom_rol_key UNIQUE (nom_rol);


--
-- TOC entry 4988 (class 2606 OID 105543)
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (cod_rol);


--
-- TOC entry 5092 (class 2606 OID 113720)
-- Name: salidas_inventario salidas_inventario_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.salidas_inventario
    ADD CONSTRAINT salidas_inventario_pkey PRIMARY KEY (cod_salida);


--
-- TOC entry 5069 (class 2606 OID 105935)
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- TOC entry 5005 (class 2606 OID 105619)
-- Name: unidades_medida unidades_medida_abr_uni_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.unidades_medida
    ADD CONSTRAINT unidades_medida_abr_uni_key UNIQUE (abr_uni);


--
-- TOC entry 5007 (class 2606 OID 105617)
-- Name: unidades_medida unidades_medida_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.unidades_medida
    ADD CONSTRAINT unidades_medida_pkey PRIMARY KEY (cod_unidad);


--
-- TOC entry 4999 (class 2606 OID 105583)
-- Name: usuario_preferencias usuario_preferencias_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuario_preferencias
    ADD CONSTRAINT usuario_preferencias_pk PRIMARY KEY (cod_usuario_upr, cod_preferencia_upr);


--
-- TOC entry 4995 (class 2606 OID 105571)
-- Name: usuarios usuarios_cor_usu_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_cor_usu_key UNIQUE (cor_usu);


--
-- TOC entry 4997 (class 2606 OID 105569)
-- Name: usuarios usuarios_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_pkey PRIMARY KEY (cod_usuario);


--
-- TOC entry 5059 (class 2606 OID 105893)
-- Name: validaciones_receta validaciones_receta_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.validaciones_receta
    ADD CONSTRAINT validaciones_receta_pkey PRIMARY KEY (cod_validacion);


--
-- TOC entry 5051 (class 2606 OID 105847)
-- Name: valoraciones valoraciones_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.valoraciones
    ADD CONSTRAINT valoraciones_pkey PRIMARY KEY (cod_valoracion);


--
-- TOC entry 5053 (class 2606 OID 105849)
-- Name: valoraciones valoraciones_usuario_receta_uk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.valoraciones
    ADD CONSTRAINT valoraciones_usuario_receta_uk UNIQUE (cod_usuario_val, cod_receta_val);


--
-- TOC entry 5061 (class 1259 OID 105920)
-- Name: bitacora_accion_fecha_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX bitacora_accion_fecha_idx ON public.bitacora USING btree (acc_bit, fec_cre_bit DESC);


--
-- TOC entry 5064 (class 1259 OID 105921)
-- Name: bitacora_usuario_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX bitacora_usuario_idx ON public.bitacora USING btree (cod_usuario_bit);


--
-- TOC entry 5046 (class 1259 OID 105812)
-- Name: historial_consulta_usuario_fecha_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX historial_consulta_usuario_fecha_idx ON public.historial_consulta USING btree (cod_usuario_con, fec_cre_con DESC);


--
-- TOC entry 5047 (class 1259 OID 105836)
-- Name: historial_resultado_consulta_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX historial_resultado_consulta_idx ON public.historial_resultado USING btree (cod_consulta_res);


--
-- TOC entry 5009 (class 1259 OID 105640)
-- Name: ingredientes_act_ing_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX ingredientes_act_ing_idx ON public.ingredientes USING btree (act_ing);


--
-- TOC entry 5027 (class 1259 OID 105731)
-- Name: ingredientes_receta_ingrediente_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX ingredientes_receta_ingrediente_idx ON public.ingredientes_receta USING btree (cod_ingrediente_inr);


--
-- TOC entry 5038 (class 1259 OID 105794)
-- Name: inventario_fec_cad_inv_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX inventario_fec_cad_inv_idx ON public.inventario USING btree (fec_cad_inv);


--
-- TOC entry 5041 (class 1259 OID 105795)
-- Name: inventario_usuario_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX inventario_usuario_idx ON public.inventario USING btree (cod_usuario_inv, act_inv);


--
-- TOC entry 5084 (class 1259 OID 105980)
-- Name: jobs_queue_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX jobs_queue_idx ON public.jobs USING btree (queue);


--
-- TOC entry 5077 (class 1259 OID 105956)
-- Name: personal_access_tokens_tokenable_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX personal_access_tokens_tokenable_idx ON public.personal_access_tokens USING btree (tokenable_type, tokenable_id);


--
-- TOC entry 5022 (class 1259 OID 105703)
-- Name: recetas_activas_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX recetas_activas_idx ON public.recetas USING btree (act_rec, ver_rec);


--
-- TOC entry 5023 (class 1259 OID 105701)
-- Name: recetas_est_val_rec_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX recetas_est_val_rec_idx ON public.recetas USING btree (est_val_rec);


--
-- TOC entry 5024 (class 1259 OID 105702)
-- Name: recetas_ori_rec_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX recetas_ori_rec_idx ON public.recetas USING btree (ori_rec);


--
-- TOC entry 5089 (class 1259 OID 113742)
-- Name: salidas_inventario_ingrediente_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX salidas_inventario_ingrediente_idx ON public.salidas_inventario USING btree (cod_ingrediente_sal);


--
-- TOC entry 5090 (class 1259 OID 113743)
-- Name: salidas_inventario_motivo_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX salidas_inventario_motivo_idx ON public.salidas_inventario USING btree (mot_sal);


--
-- TOC entry 5093 (class 1259 OID 113741)
-- Name: salidas_inventario_usuario_fecha_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX salidas_inventario_usuario_fecha_idx ON public.salidas_inventario USING btree (cod_usuario_sal, fec_sal DESC);


--
-- TOC entry 5067 (class 1259 OID 105937)
-- Name: sessions_last_activity_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sessions_last_activity_idx ON public.sessions USING btree (last_activity);


--
-- TOC entry 5070 (class 1259 OID 105936)
-- Name: sessions_user_id_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sessions_user_id_idx ON public.sessions USING btree (user_id);


--
-- TOC entry 5008 (class 1259 OID 105620)
-- Name: unidades_medida_tip_uni_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX unidades_medida_tip_uni_idx ON public.unidades_medida USING btree (tip_uni);


--
-- TOC entry 4993 (class 1259 OID 105577)
-- Name: usuarios_act_usu_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX usuarios_act_usu_idx ON public.usuarios USING btree (act_usu);


--
-- TOC entry 5060 (class 1259 OID 105904)
-- Name: validaciones_receta_receta_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX validaciones_receta_receta_idx ON public.validaciones_receta USING btree (cod_receta_vre, fec_rev_vre DESC);


--
-- TOC entry 5118 (class 2606 OID 105915)
-- Name: bitacora bitacora_usuario_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.bitacora
    ADD CONSTRAINT bitacora_usuario_fk FOREIGN KEY (cod_usuario_bit) REFERENCES public.usuarios(cod_usuario) ON DELETE SET NULL;


--
-- TOC entry 5114 (class 2606 OID 105875)
-- Name: favoritos favoritos_receta_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.favoritos
    ADD CONSTRAINT favoritos_receta_fk FOREIGN KEY (cod_receta_fav) REFERENCES public.recetas(cod_receta) ON DELETE CASCADE;


--
-- TOC entry 5115 (class 2606 OID 105870)
-- Name: favoritos favoritos_usuario_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.favoritos
    ADD CONSTRAINT favoritos_usuario_fk FOREIGN KEY (cod_usuario_fav) REFERENCES public.usuarios(cod_usuario) ON DELETE CASCADE;


--
-- TOC entry 5109 (class 2606 OID 105807)
-- Name: historial_consulta historial_consulta_usuario_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.historial_consulta
    ADD CONSTRAINT historial_consulta_usuario_fk FOREIGN KEY (cod_usuario_con) REFERENCES public.usuarios(cod_usuario) ON DELETE CASCADE;


--
-- TOC entry 5110 (class 2606 OID 105826)
-- Name: historial_resultado historial_resultado_consulta_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.historial_resultado
    ADD CONSTRAINT historial_resultado_consulta_fk FOREIGN KEY (cod_consulta_res) REFERENCES public.historial_consulta(cod_consulta) ON DELETE CASCADE;


--
-- TOC entry 5111 (class 2606 OID 105831)
-- Name: historial_resultado historial_resultado_receta_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.historial_resultado
    ADD CONSTRAINT historial_resultado_receta_fk FOREIGN KEY (cod_receta_res) REFERENCES public.recetas(cod_receta) ON DELETE SET NULL;


--
-- TOC entry 5097 (class 2606 OID 105635)
-- Name: ingredientes ingredientes_categoria_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ingredientes
    ADD CONSTRAINT ingredientes_categoria_fk FOREIGN KEY (cod_categoria_ingrediente_ing) REFERENCES public.categorias_ingrediente(cod_categoria_ingrediente) ON DELETE RESTRICT;


--
-- TOC entry 5100 (class 2606 OID 105721)
-- Name: ingredientes_receta ingredientes_receta_ingrediente_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ingredientes_receta
    ADD CONSTRAINT ingredientes_receta_ingrediente_fk FOREIGN KEY (cod_ingrediente_inr) REFERENCES public.ingredientes(cod_ingrediente) ON DELETE RESTRICT;


--
-- TOC entry 5101 (class 2606 OID 105716)
-- Name: ingredientes_receta ingredientes_receta_receta_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ingredientes_receta
    ADD CONSTRAINT ingredientes_receta_receta_fk FOREIGN KEY (cod_receta_inr) REFERENCES public.recetas(cod_receta) ON DELETE CASCADE;


--
-- TOC entry 5102 (class 2606 OID 105726)
-- Name: ingredientes_receta ingredientes_receta_unidad_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ingredientes_receta
    ADD CONSTRAINT ingredientes_receta_unidad_fk FOREIGN KEY (cod_unidad_inr) REFERENCES public.unidades_medida(cod_unidad) ON DELETE RESTRICT;


--
-- TOC entry 5106 (class 2606 OID 105784)
-- Name: inventario inventario_ingrediente_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inventario
    ADD CONSTRAINT inventario_ingrediente_fk FOREIGN KEY (cod_ingrediente_inv) REFERENCES public.ingredientes(cod_ingrediente) ON DELETE CASCADE;


--
-- TOC entry 5107 (class 2606 OID 105789)
-- Name: inventario inventario_unidad_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inventario
    ADD CONSTRAINT inventario_unidad_fk FOREIGN KEY (cod_unidad_inv) REFERENCES public.unidades_medida(cod_unidad) ON DELETE RESTRICT;


--
-- TOC entry 5108 (class 2606 OID 105779)
-- Name: inventario inventario_usuario_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inventario
    ADD CONSTRAINT inventario_usuario_fk FOREIGN KEY (cod_usuario_inv) REFERENCES public.usuarios(cod_usuario) ON DELETE CASCADE;


--
-- TOC entry 5103 (class 2606 OID 105745)
-- Name: pasos_receta pasos_receta_receta_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pasos_receta
    ADD CONSTRAINT pasos_receta_receta_fk FOREIGN KEY (cod_receta_pas) REFERENCES public.recetas(cod_receta) ON DELETE CASCADE;


--
-- TOC entry 5104 (class 2606 OID 105761)
-- Name: receta_etiquetas receta_etiquetas_etiqueta_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.receta_etiquetas
    ADD CONSTRAINT receta_etiquetas_etiqueta_fk FOREIGN KEY (cod_etiqueta_ret) REFERENCES public.etiquetas(cod_etiqueta) ON DELETE CASCADE;


--
-- TOC entry 5105 (class 2606 OID 105756)
-- Name: receta_etiquetas receta_etiquetas_receta_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.receta_etiquetas
    ADD CONSTRAINT receta_etiquetas_receta_fk FOREIGN KEY (cod_receta_ret) REFERENCES public.recetas(cod_receta) ON DELETE CASCADE;


--
-- TOC entry 5098 (class 2606 OID 105696)
-- Name: recetas recetas_autor_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.recetas
    ADD CONSTRAINT recetas_autor_fk FOREIGN KEY (cod_usuario_rec) REFERENCES public.usuarios(cod_usuario) ON DELETE SET NULL;


--
-- TOC entry 5099 (class 2606 OID 105691)
-- Name: recetas recetas_categoria_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.recetas
    ADD CONSTRAINT recetas_categoria_fk FOREIGN KEY (cod_categoria_receta_rec) REFERENCES public.categorias_receta(cod_categoria_receta) ON DELETE RESTRICT;


--
-- TOC entry 5119 (class 2606 OID 113726)
-- Name: salidas_inventario salidas_inventario_ingrediente_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.salidas_inventario
    ADD CONSTRAINT salidas_inventario_ingrediente_fk FOREIGN KEY (cod_ingrediente_sal) REFERENCES public.ingredientes(cod_ingrediente) ON DELETE RESTRICT;


--
-- TOC entry 5120 (class 2606 OID 113736)
-- Name: salidas_inventario salidas_inventario_receta_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.salidas_inventario
    ADD CONSTRAINT salidas_inventario_receta_fk FOREIGN KEY (cod_receta_sal) REFERENCES public.recetas(cod_receta) ON DELETE SET NULL;


--
-- TOC entry 5121 (class 2606 OID 113731)
-- Name: salidas_inventario salidas_inventario_unidad_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.salidas_inventario
    ADD CONSTRAINT salidas_inventario_unidad_fk FOREIGN KEY (cod_unidad_sal) REFERENCES public.unidades_medida(cod_unidad) ON DELETE RESTRICT;


--
-- TOC entry 5122 (class 2606 OID 113721)
-- Name: salidas_inventario salidas_inventario_usuario_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.salidas_inventario
    ADD CONSTRAINT salidas_inventario_usuario_fk FOREIGN KEY (cod_usuario_sal) REFERENCES public.usuarios(cod_usuario) ON DELETE CASCADE;


--
-- TOC entry 5095 (class 2606 OID 105589)
-- Name: usuario_preferencias usuario_preferencias_preferencia_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuario_preferencias
    ADD CONSTRAINT usuario_preferencias_preferencia_fk FOREIGN KEY (cod_preferencia_upr) REFERENCES public.preferencias_dieta(cod_preferencia) ON DELETE CASCADE;


--
-- TOC entry 5096 (class 2606 OID 105584)
-- Name: usuario_preferencias usuario_preferencias_usuario_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuario_preferencias
    ADD CONSTRAINT usuario_preferencias_usuario_fk FOREIGN KEY (cod_usuario_upr) REFERENCES public.usuarios(cod_usuario) ON DELETE CASCADE;


--
-- TOC entry 5094 (class 2606 OID 105572)
-- Name: usuarios usuarios_rol_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_rol_fk FOREIGN KEY (cod_rol_usu) REFERENCES public.roles(cod_rol) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 5116 (class 2606 OID 105899)
-- Name: validaciones_receta validaciones_receta_nutricionista_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.validaciones_receta
    ADD CONSTRAINT validaciones_receta_nutricionista_fk FOREIGN KEY (cod_usuario_vre) REFERENCES public.usuarios(cod_usuario) ON DELETE RESTRICT;


--
-- TOC entry 5117 (class 2606 OID 105894)
-- Name: validaciones_receta validaciones_receta_receta_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.validaciones_receta
    ADD CONSTRAINT validaciones_receta_receta_fk FOREIGN KEY (cod_receta_vre) REFERENCES public.recetas(cod_receta) ON DELETE CASCADE;


--
-- TOC entry 5112 (class 2606 OID 105855)
-- Name: valoraciones valoraciones_receta_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.valoraciones
    ADD CONSTRAINT valoraciones_receta_fk FOREIGN KEY (cod_receta_val) REFERENCES public.recetas(cod_receta) ON DELETE CASCADE;


--
-- TOC entry 5113 (class 2606 OID 105850)
-- Name: valoraciones valoraciones_usuario_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.valoraciones
    ADD CONSTRAINT valoraciones_usuario_fk FOREIGN KEY (cod_usuario_val) REFERENCES public.usuarios(cod_usuario) ON DELETE CASCADE;


--
-- TOC entry 5329 (class 0 OID 0)
-- Dependencies: 5
-- Name: SCHEMA public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE USAGE ON SCHEMA public FROM PUBLIC;
GRANT ALL ON SCHEMA public TO PUBLIC;


-- Completed on 2026-10-11 22:07:54

--
-- PostgreSQL database dump complete
--

\unrestrict zh9xvcgOZBvHfDjiSq44Tpv5abq3m0ocdD4FU7Ru245icsYIHDxM91lm2Gnubn3

