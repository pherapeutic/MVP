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
  const { info, setRightAnswer, canSelect } = props;

  const { id, title } = info;
  return (
    <TouchableOpacity
      style={[styles.answerWrap, { borderColor: selected ? constants.colors.lightGreen : '#ffffff' }]}
      onPress={() => {
        if (canSelect) {
          setSelected(true)
          setRightAnswer(info)
        }
      }}
    >
      <Text style={[styles.answerText, { color: selected ? constants.colors.lightGreen : '#ffffff' }]} >{title}</Text>
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

  console.log('props in question item => ', props);
  console.log('right answer  ===> ', rightAnswer);

  const { data, selectAnswer } = props;
  const { answer, id, status, title } = data;

  useEffect(() => {
    if (rightAnswer)
      selectAnswer(rightAnswer)
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