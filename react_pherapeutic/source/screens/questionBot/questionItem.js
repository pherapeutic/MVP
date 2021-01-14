import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  Image,
  TouchableOpacity,
  Dimensions,
  ScrollView
} from 'react-native';
import { connect } from 'react-redux';
import constants from '../../utils/constants';
import styles from './styles';

const AnswerItem = (props) => {
  const [selected, setSelected] = useState(false)
  const { info, setRightAnswer, canSelect,selectedID } = props;

  const { id, title } = info;
  useEffect(() => {
    setSelected(false)
    console.log('asa', info)

  }, []);

  const answeradd = (info) => {
      setSelected(true)
      setRightAnswer(info)
  };

  return (
    <TouchableOpacity
      style={[styles.answerWrap, { borderColor: selectedID==info.id ? constants.colors.lightGreen : '#ffffff' }]}
      onPress={() => {
        answeradd(info)
      }}
    >
      <Text style={[styles.answerText, { color: selectedID==info.id ? constants.colors.lightGreen : '#ffffff' }]} >{title}</Text>
    </TouchableOpacity>
  )
}

const SelectedItem = (props) => {

  return (
    <View style={styles.selectedWrap} >
      <Text style={styles.selectedText} >{props.title}</Text>
    </View>
  )
}

const QuestionItem = (props) => {
  const [rightAnswer, setRightAnswer] = useState(null)
  const [selectedID, setselectedID] = useState('')
  const { data, selectAnswer } = props;
  const { answer, id, status, title } = data;
   useEffect(() => {
    if (rightAnswer)
    {
      setselectedID(rightAnswer.id)
      selectAnswer(rightAnswer)
    }

  }, [rightAnswer]);
  return (
    <View style={styles.questionItemContainer} >
      <View style={styles.questionWrap} >
        <Text style={styles.questionText} >{title}</Text>
      </View>
      <View style={styles.answersView} >
        {
          answer.map(answerData => <AnswerItem
            canSelect={!rightAnswer}
            setRightAnswer={setRightAnswer}
            info={answerData}
            selectedID={selectedID}
          />)
        }
      </View>
      {
        rightAnswer
          ?
          <View style={styles.choiceView} >
            <SelectedItem title={rightAnswer['title']} />
          </View>
          :
          <View />
      }
    </View>
  )
}

export default QuestionItem;