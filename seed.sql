--
-- PostgreSQL database dump
--

-- Dumped from database version 14.5 (Ubuntu 14.5-0ubuntu0.22.04.1)
-- Dumped by pg_dump version 14.5 (Ubuntu 14.5-0ubuntu0.22.04.1)

-- Started on 2022-12-23 08:43:37 WAT

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--DROP DATABASE lms;
--
-- TOC entry 3523 (class 1262 OID 33504)
-- Name: lms; Type: DATABASE; Schema: -; Owner: postgres
--

CREATE DATABASE lms WITH TEMPLATE = template0 ENCODING = 'UTF8' LOCALE = 'en_US.UTF-8';


ALTER DATABASE lms OWNER TO postgres;

\connect lms

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 210 (class 1259 OID 33506)
-- Name: account; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.account (
    full_name character varying(50),
    email character varying(100),
    identification_no character varying(20),
    account_type character varying(16),
    password character varying,
    id integer NOT NULL
);


ALTER TABLE public.account OWNER TO postgres;

--
-- TOC entry 209 (class 1259 OID 33505)
-- Name: account_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

ALTER TABLE public.account ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.account_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- TOC entry 218 (class 1259 OID 33568)
-- Name: examiner_marking; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.examiner_marking (
    submission_section_id integer,
    score numeric(5,2),
    examiner_id integer
);


ALTER TABLE public.examiner_marking OWNER TO postgres;

--
-- TOC entry 214 (class 1259 OID 33528)
-- Name: section; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.section (
    id integer NOT NULL,
    name character varying NOT NULL,
    max_length integer,
    max_score integer
);


ALTER TABLE public.section OWNER TO postgres;

--
-- TOC entry 213 (class 1259 OID 33527)
-- Name: section_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

ALTER TABLE public.section ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.section_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- TOC entry 219 (class 1259 OID 33581)
-- Name: sessions; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sessions (
    account_id integer NOT NULL,
    session_id character varying(64) NOT NULL,
    account_type character varying(16) NOT NULL,
    expiry timestamp with time zone NOT NULL
);


ALTER TABLE public.sessions OWNER TO postgres;

--
-- TOC entry 212 (class 1259 OID 33514)
-- Name: submission; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.submission (
    title character varying,
    submitted_by integer NOT NULL,
    id integer NOT NULL,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.submission OWNER TO postgres;

--
-- TOC entry 217 (class 1259 OID 33553)
-- Name: submission_examiners; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.submission_examiners (
    submission_id integer NOT NULL,
    examiner_id integer NOT NULL
);


ALTER TABLE public.submission_examiners OWNER TO postgres;

--
-- TOC entry 211 (class 1259 OID 33513)
-- Name: submission_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

ALTER TABLE public.submission ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.submission_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- TOC entry 216 (class 1259 OID 33536)
-- Name: submission_section; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.submission_section (
    submission_id integer NOT NULL,
    section_id integer NOT NULL,
    text text,
    id integer NOT NULL
);


ALTER TABLE public.submission_section OWNER TO postgres;

--
-- TOC entry 215 (class 1259 OID 33535)
-- Name: submission_section_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

ALTER TABLE public.submission_section ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.submission_section_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- TOC entry 3360 (class 2606 OID 33512)
-- Name: account account_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.account
    ADD CONSTRAINT account_pk PRIMARY KEY (id);


--
-- TOC entry 3364 (class 2606 OID 33534)
-- Name: section section_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.section
    ADD CONSTRAINT section_pk PRIMARY KEY (id);


--
-- TOC entry 3370 (class 2606 OID 33585)
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (account_id);


--
-- TOC entry 3368 (class 2606 OID 33557)
-- Name: submission_examiners submission_examiners_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.submission_examiners
    ADD CONSTRAINT submission_examiners_pk PRIMARY KEY (submission_id, examiner_id);


--
-- TOC entry 3362 (class 2606 OID 33521)
-- Name: submission submission_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.submission
    ADD CONSTRAINT submission_pk PRIMARY KEY (id);


--
-- TOC entry 3366 (class 2606 OID 33542)
-- Name: submission_section submission_section_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.submission_section
    ADD CONSTRAINT submission_section_pk PRIMARY KEY (id);


--
-- TOC entry 3378 (class 2606 OID 33586)
-- Name: sessions account_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT account_fk FOREIGN KEY (account_id) REFERENCES public.account(id);


--
-- TOC entry 3376 (class 2606 OID 33571)
-- Name: examiner_marking examiner_marking_account_null_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.examiner_marking
    ADD CONSTRAINT examiner_marking_account_null_fk FOREIGN KEY (examiner_id) REFERENCES public.account(id);


--
-- TOC entry 3377 (class 2606 OID 33576)
-- Name: examiner_marking examiner_marking_submission_section_null_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.examiner_marking
    ADD CONSTRAINT examiner_marking_submission_section_null_fk FOREIGN KEY (submission_section_id) REFERENCES public.submission_section(id);


--
-- TOC entry 3371 (class 2606 OID 33522)
-- Name: submission submission_account_null_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.submission
    ADD CONSTRAINT submission_account_null_fk FOREIGN KEY (submitted_by) REFERENCES public.account(id);


--
-- TOC entry 3375 (class 2606 OID 33563)
-- Name: submission_examiners submission_examiners_account_null_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.submission_examiners
    ADD CONSTRAINT submission_examiners_account_null_fk FOREIGN KEY (examiner_id) REFERENCES public.account(id);


--
-- TOC entry 3374 (class 2606 OID 33558)
-- Name: submission_examiners submission_examiners_submission_null_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.submission_examiners
    ADD CONSTRAINT submission_examiners_submission_null_fk FOREIGN KEY (submission_id) REFERENCES public.submission(id);


--
-- TOC entry 3372 (class 2606 OID 33543)
-- Name: submission_section submission_section_section_null_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.submission_section
    ADD CONSTRAINT submission_section_section_null_fk FOREIGN KEY (section_id) REFERENCES public.section(id);


--
-- TOC entry 3373 (class 2606 OID 33548)
-- Name: submission_section submission_section_submission_null_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.submission_section
    ADD CONSTRAINT submission_section_submission_null_fk FOREIGN KEY (submission_id) REFERENCES public.submission(id);


-- Completed on 2022-12-23 08:43:37 WAT

--
-- PostgreSQL database dump complete
--

