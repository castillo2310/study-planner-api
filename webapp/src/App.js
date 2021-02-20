import React from 'react';

import 'bootstrap/dist/css/bootstrap.min.css';
import {Container, Row, Col, Navbar, Form, InputGroup, FormControl, Button, Modal} from 'react-bootstrap';
import Select from 'react-select';
import Typed from 'typed.js';
import moment from 'moment';
import { toast } from 'react-toastify'
import 'react-toastify/dist/ReactToastify.css';
import Spinner from "react-bootstrap/Spinner";
import './App.css';

import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";
import { faPlus, faBookOpen } from '@fortawesome/free-solid-svg-icons'

toast.configure({
    hideProgressBar: true
});
class App extends React.Component{

    weekdays = [
        {value: 1, label:'Monday'},
        {value: 2, label:'Tuesday'},
        {value: 3, label:'Wednesday'},
        {value: 4, label:'Thursday'},
        {value: 5, label:'Friday'},
        {value: 6, label:'Saturday'},
        {value: 7, label:'Sunday'},
    ];

    constructor() {
        super();

        this.state = {
            startDate: '',
            endDate: '',
            dailyStudyHours: null,
            allowedWeekDays: [],
            chapters: [],
            chapter: {
                description: '',
                pages: ''
            },
            loading: false,
            pdfLink: '',
            showModal: false,
            scrollDown: false
        };

        this.handleAddChapter = this.handleAddChapter.bind(this);
        this.handleChange = this.handleChange.bind(this);
        this.handleSelectChange = this.handleSelectChange.bind(this);
        this.handleChapterChange = this.handleChapterChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
        this.handleCloseModal = this.handleCloseModal.bind(this);
        this.handleScroll = this.handleScroll.bind(this);
    }

    handleAddChapter = () => {
        const description = this.state.chapter.description;
        const pages = this.state.chapter.pages;

        if (!pages || !description) {
            return toast.error('Please, enter a valid chapter!');
        }

        this.setState(state => {
            return {
                chapter: {
                    description: '',
                    pages: ''
                },
                chapters: state.chapters.concat({
                    description: description,
                    pages: pages
                })
            };
        }, this.showResult)

        toast.success('Chapter added!');
    };

    handleCloseModal = () => {
        this.setState({
            showModal: false
        })
    };

    handleSubmit = async(event) => {
        event.preventDefault();

        const url = 'http://127.0.0.1:7070/api/plan/create';
        //const url = ' https://cors-anywhere.herokuapp.com/http://15.237.39.82/plan/create';

        const body = {
            startDate: this.state.startDate,
            endDate: this.state.endDate,
            dailyStudyHours: this.state.dailyStudyHours,
            allowedWeekDays: this.state.allowedWeekDays,
            chapters: this.state.chapters
        };

        if (this.state.chapter.description && this.state.chapter.pages) {
            body.chapters.push({
                description: this.state.chapter.description,
                pages: this.state.chapter.pages
            });
        }

        try{
            this.setState({
                loading: true
            });

            const data = await fetch(url, {
                method: 'POST',
                mode: 'cors',
                body: JSON.stringify(body),
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            const contentType = data.headers.get("content-type");
            if (contentType === 'application/json') {
                const errorData = await data.json();
                throw new Error(errorData.error);
            }

            const blob = await data.blob();

            this.setState({
                pdfLink: URL.createObjectURL(blob),
                loading: false,
                showModal: true
            });
        } catch (e) {
            toast.error(e.message);

            this.setState({
                loading: false
            });
        }
    };

    showResult = () => {
        //TODO: One Typed per form element

        const stringList = [];

        if (this.state.startDate && !this.state.endDate) {
            stringList.push(`You will start studying on ${moment(this.state.startDate).format('ll')}.`);
        } else if (!this.state.startDate && this.state.endDate) {
            stringList.push(`You will end studying on ${moment(this.state.endDate).format('ll')}.`);
        }else if (this.state.startDate && this.state.endDate) {
            stringList.push(`You will be studying from ${moment(this.state.startDate).format('ll')} to  ${moment(this.state.endDate).format('ll')}.`);
        }

        if (this.state.dailyStudyHours) {
            stringList.push(`You will study ${this.state.dailyStudyHours} hours per day.`);
        }

        if(this.typed && this.typed.constructor === Typed) {
            this.typed.destroy();
        }

        if (this.state.allowedWeekDays.length > 0) {
            const selectedWeekDays = this.weekdays.filter(e => this.state.allowedWeekDays.includes(e.value)).map(e => e.label);
            const weekDaysString = selectedWeekDays.length > 1 ? selectedWeekDays.slice(0, -1).join(', ')+' and '+selectedWeekDays.slice(-1) : selectedWeekDays;
            stringList.push(`You will study on ${weekDaysString}.`);
        }

        if (this.state.chapters.length > 0) {
            const chaptersString = this.state.chapters
                .map(chapter => `&emsp;- ${chapter.description}: ${chapter.pages} pages`)
                .join('<br>');
            stringList.push(`You will study the following chapters: <br> ${chaptersString}`);
        }

        if (stringList.length > 0) {

            const string = [stringList.join('<br>')];
            const options = {
                strings: string,
                typeSpeed: 20,
                showCursor: true,
                onComplete: function (self) {
                    self.stop();
                }
            };

            this.typed = new Typed(this.typedElement, options);
        }
    };

    handleChange = (event) => {
        const name = event.target.name;
        const value = event.target.value;
        this.setState(state => {
            return state[name] = value;
        }, this.showResult)
    };

    handleSelectChange = (options) => {
        this.setState({
            allowedWeekDays: options.map(option => option.value)
        }, this.showResult)
    };

    handleChapterChange = (event) => {
        const name = event.target.name;
        const value = event.target.value;

        this.setState(state => {
            return state['chapter'][name] = value;
        })
    };

    componentDidMount = () => {
        window.addEventListener('scroll', this.handleScroll);
    };

    handleScroll = () => {
        this.setState({
            scrollDown: window.scrollY > 60
        });
    };

    render() {
        return (
            <>
                <Container>
                    <Navbar fixed="top" variant="dark" className={`top-menu ${this.state.scrollDown ? 'onScroll' : ''} `}>
                        <Navbar.Brand href="#home">
                            {/*<img
                            alt=""
                            src="/logo.svg"
                            width="30"
                            height="30"
                            className="d-inline-block align-top"
                        />*/}
                            Study Planner
                        </Navbar.Brand>
                    </Navbar>
                </Container>
                <section id={'main'} className={'pt-5 pt-lg-0'}>
                    <Container className={'h-100'}>
                        <Row className={'h-100'}>
                            <Col lg={5} className={'my-auto'} style={{maxWidth:'500px'}}>
                                <div className={'mx-auto'}>
                                    <h1 className={'display-4 text-light pb-2'}>
                                        <span className={'font-weight-light'}>Plan </span>
                                         your study!
                                    </h1>
                                    <h4 className={'font-weight-light text-light pb-2'}>
                                        Enter the required data and download your study plan.
                                    </h4>
                                    <Button href="#form" className={'btn-custom'} variant="light" size="lg" type="button">
                                        Get started! <FontAwesomeIcon icon={faBookOpen} color={'rgb(65, 74, 191)'}/>
                                    </Button>
                                </div>
                            </Col>
                            <Col lg={{span: 5, offset: 2}} className={'my-auto'}>
                            </Col>
                        </Row>
                    </Container>
                </section>
                <section id={'form'} className={'pt-5'}>
                    <Container className={'h-100'}>
                        <Row className={'h-100'}>
                            <Col lg={5} className={'my-auto'}>
                                <Form onSubmit={this.handleSubmit}>
                                    <Form.Group >
                                        <Form.Label>Enter start and end date</Form.Label>
                                        <Form.Row>
                                            <Col>
                                                <Form.Control type="date" name="startDate" onChange={this.handleChange} required />
                                            </Col>
                                            <Col>
                                                <Form.Control type="date" name="endDate" onChange={this.handleChange} required/>
                                            </Col>
                                        </Form.Row>
                                    </Form.Group>
                                    <Form.Group>
                                        <Form.Label>¿How many hours will you study per day?</Form.Label>
                                        <Form.Control type="number" name="dailyStudyHours" min={1} max={12}
                                                      onChange={this.handleChange} required/>
                                    </Form.Group>
                                    <Form.Group>
                                        <Form.Label>¿How many days will you study per week?</Form.Label>
                                        <Select options={this.weekdays} isMulti="true" closeMenuOnSelect="false" isSearchable="true"
                                                name="allowedWeekDays"  onChange={this.handleSelectChange} required/>
                                    </Form.Group>
                                    <Form.Group>
                                        <Form.Label>Enter the chapters</Form.Label>
                                        <InputGroup>
                                            <FormControl placeholder="Description" name='description'
                                                         onChange={this.handleChapterChange}
                                                        value={this.state.chapter.description}/>

                                            <FormControl placeholder="Pages" type="number" name='pages' style={{borderTopRightRadius:0, borderBottomRightRadius:0}}
                                                         onChange={this.handleChapterChange}
                                                         value={this.state.chapter.pages}/>

                                            <Button variant="primary" type="button" onClick={this.handleAddChapter}
                                                    style={{borderTopLeftRadius:0, borderBottomLeftRadius:0}}>
                                                <FontAwesomeIcon icon={faPlus} color={'white'}/>
                                            </Button>
                                        </InputGroup>

                                    </Form.Group>
                                    <Form.Group>

                                    </Form.Group>
                                    <Button disabled={this.state.loading} className='btn-custom' variant="outline-primary" type="submit" block>
                                        {
                                            this.state.loading
                                                ? (<span><Spinner as="span" animation="border"/> Loading ...</span>)
                                                : 'Generate plan'
                                        }
                                    </Button>
                                </Form>
                            </Col>
                            <Col lg={{span: 6, offset: 1}} className={'my-auto'}>
                               <span id={'result-text'} className={'mx-auto'}
                                    ref={(el) => { this.typedElement = el; }}>

                               </span>
                            </Col>
                        </Row>
                    </Container>
                </section>

                <Modal
                    show={this.state.showModal}
                    onHide={this.handleCloseModal}
                >
                    <Modal.Header closeButton>
                        <h4>Successfully generated plan</h4>
                    </Modal.Header>
                    <Modal.Body className={'text-center'}>
                        <p className={'mb-0'}>Download your plan as PDF and start studying!</p>
                        <Button className={'my-3 btn-custom'} variant="outline-success" target="_blank" href={this.state.pdfLink} >
                            Download
                        </Button>
                    </Modal.Body>
                </Modal>
            </>
        );
    }

}

export default App;
